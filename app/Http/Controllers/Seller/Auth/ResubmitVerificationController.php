<?php

namespace App\Http\Controllers\Seller\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Seller;
use App\Models\SellerDocument;
use App\Models\Notification;
use App\Services\BrevoMailService;
use App\Services\CloudinaryService;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ResubmitVerificationController extends Controller
{
    public function resubmit(
        Request $request,
        BrevoMailService $brevo,
        CloudinaryService $cloudinary
    ) {
        try {
            DB::beginTransaction();
            
            $seller = auth('seller')->user();

            // Only allow resubmission if rejected
            if ($seller->verification_status !== 'rejected') {
                return redirect()->route('seller.dashboard')
                    ->with('error', 'Your account is not in a state that allows resubmission.');
            }

            $request->validate([
                'first_name'          => ['required', 'string', 'max:100'],
                'last_name'           => ['required', 'string', 'max:100'],
                'email'               => ['required', 'email', 'unique:sellers,email,' . $seller->id],
                'phone'               => ['required', 'string', 'max:20'],
                'business_name'       => ['required', 'string', 'max:200'],
                'business_address'    => ['nullable', 'string'],
                'address_code'        => ['nullable', 'string'],
                'password'            => ['nullable', 'confirmed', Password::min(8)],
                'terms'               => ['accepted'],
                'document_type'       => ['nullable', 'string'],
                'document_file'       => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
                'keep_existing_document' => ['nullable', 'boolean'],
            ]);

            // Determine if seller should be verified business
            $hasDocument = false;
            $existingDocument = $seller->document()->first();
            
            // Check if they uploaded a new document OR have an existing document and choose to keep it
            if ($request->hasFile('document_file')) {
                $hasDocument = true;
            } 
            
            // Log::info('Resubmit data', [
            //     'seller_id' => $seller->id,
            //     'has_document' => $hasDocument,
            //     'keep_existing' => $request->boolean('keep_existing_document'),
            //     'has_file' => $request->hasFile('document_file')
            // ]);

            // Update seller information
            $updateData = [
                'first_name'          => $request->first_name,
                'last_name'           => $request->last_name,
                'email'               => $request->email,
                'phone'               => $request->phone,
                'business_name'       => $request->business_name,
                'business_address'    => $request->business_address,
                'address_code'        => $request->address_code,
                'is_verified_business' => $hasDocument,
                'verification_status' => 'pending',
                'is_approved'         => false,
                'rejection_reason'    => null,
                'rejected_at'         => null,
                'rejected_by'         => null,
            ];

            // Update password if provided
            if ($request->filled('password')) {
                $updateData['password'] = bcrypt($request->password);
            }

            // Update seller
            $updated = $seller->update($updateData);
            
            Log::info('Seller update result', [
                'updated' => $updated,
                'data' => $updateData
            ]);

            // Refresh seller model to get updated data
            $seller->refresh();

            // Update business slug if business name changed
            if ($seller->wasChanged('business_name')) {
                $slug = Str::slug($request->business_name);
                $originalSlug = $slug;
                $count = 1;
                while (Seller::where('business_slug', $slug)->where('id', '!=', $seller->id)->exists()) {
                    $slug = $originalSlug . '-' . $count++;
                }
                $seller->update(['business_slug' => $slug]);
            }

            // Handle document for verified sellers
            if ($hasDocument) {
                // Check if user wants to keep existing document and no new file uploaded
                $keepExisting = !$request->hasFile('document_file');
                
                if ($keepExisting && $existingDocument) {
                    // Update document type if changed
                    $documentUpdateData = ['status' => 'pending'];
                    if ($request->document_type && $request->document_type !== $existingDocument->document_type) {
                        $documentUpdateData['document_type'] = $request->document_type;
                    }
                    if ($request->filled('rejection_reason')) {
                        $documentUpdateData['rejection_reason'] = null;
                    }
                    $existingDocument->update($documentUpdateData);
                    
                    Log::info('Updated existing document', $documentUpdateData);
                    
                } elseif ($request->hasFile('document_file')) {
                    // Upload new document
                    try {
                        $uploaded = $cloudinary->uploadDocument(
                            $request->file('document_file'),
                            'orderer/seller-docs'
                        );
                        
                        // Delete old document if exists
                        if ($existingDocument) {
                            $existingDocument->delete();
                        }
                        
                        // Create new document
                        SellerDocument::create([
                            'seller_id'            => $seller->id,
                            'document_type'        => $request->document_type,
                            'document_url'         => $uploaded['url'],
                            'cloudinary_public_id' => $uploaded['public_id'],
                            'original_filename'    => $request->file('document_file')->getClientOriginalName(),
                            'status'               => 'pending',
                        ]);
                        
                        Log::info('Created new document for seller', [
                            'seller_id' => $seller->id,
                            'document_type' => $request->document_type
                        ]);
                        
                    } catch (\Exception $e) {
                        Log::error('Document upload failed', [
                            'seller_id' => $seller->id,
                            'error' => $e->getMessage()
                        ]);
                        DB::rollBack();
                        return back()->with('error', 'Failed to upload document. Please try again.');
                    }
                }
            } else {
                // If no document, delete existing document if any
                if ($existingDocument) {
                    $existingDocument->delete();
                    Log::info('Deleted existing document for seller', ['seller_id' => $seller->id]);
                }
            }

            DB::commit();

            // Send notification to admin that seller resubmitted
            Notification::create([
                'notifiable_type' => 'App\Models\Admin',
                'notifiable_id'   => 1,
                'type'            => 'seller_resubmitted',
                'title'           => 'Seller Resubmitted Application',
                'body'            => "Seller {$seller->business_name} has resubmitted their application for review.",
                'action_url'      => route('admin.sellers.show', $seller->id),
            ]);

            // Send confirmation email
            //$brevo->sendSellerResubmitted($seller);

            return redirect()->route('seller.pending')
                ->with('success', 'Your application has been resubmitted for review.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Resubmit error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }
}