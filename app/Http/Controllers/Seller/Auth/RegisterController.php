<?php

namespace App\Http\Controllers\Seller\Auth;

use App\Http\Controllers\Controller;
use App\Models\Marketer;
use App\Models\Seller;
use App\Models\SellerDocument;
use App\Services\BrevoMailService;
use App\Services\CloudinaryService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

class RegisterController extends Controller
{
    public function showForm()
    {
        return view('seller.auth.register');
    }

    public function register(
        Request $request,
        BrevoMailService $brevo,
        CloudinaryService $cloudinary
    ) {
        $request->validate([
            'first_name'           => ['required', 'string', 'max:100'],
            'last_name'            => ['required', 'string', 'max:100'],
            'email'                => ['required', 'email', 'unique:sellers,email'],
            'phone'                => ['required', 'string', 'max:20'],
            'business_name'        => ['required', 'string', 'max:200'],
            'business_address'     => ['nullable', 'string'],
            'address_code'         => ['nullable', 'string'],
            'password'             => ['required', 'confirmed', Password::min(8)],
            'is_verified_business' => ['required', 'in:0,1'],
            'terms'                => ['accepted'],
            'document_type'        => ['required_if:is_verified_business,1'],
            'document_file'        => [
                'required_if:is_verified_business,1',
                'file',
                'mimes:pdf,jpg,jpeg,png',
                'max:5120',
            ],
        ]);

        $isVerified = $request->is_verified_business == '1';

        // ── Slug ──────────────────────────────────────────────────────────────
        $slug = Str::slug($request->business_name);
        $originalSlug = $slug;
        $count = 1;
        while (Seller::where('business_slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $count++;
        }

        // ── Referral / Marketer code resolution ───────────────────────────────
        $referredBy  = null;
        $marketerId  = null;
        $referralCode = $request->referral_code;

        if ($referralCode) {
            if (str_starts_with(strtoupper($referralCode), 'OR-MRT-')) {
                // It's a marketer code
                $marketer = Marketer::where('marketing_code', strtoupper($referralCode))
                                    ->where('is_active', true)
                                    ->first();
                if ($marketer) {
                    $marketerId = $marketer->id;
                }
            } else {
                // It's a seller referral code
                $referrer = Seller::where('referral_code', $referralCode)->first();
                if ($referrer) {
                    $referredBy = $referrer->id;
                }
            }
        }

        // ── Create seller ─────────────────────────────────────────────────────
        $seller = Seller::create([
            'first_name'           => $request->first_name,
            'last_name'            => $request->last_name,
            'email'                => $request->email,
            'phone'                => $request->phone,
            'password'             => $request->password,
            'business_name'        => $request->business_name,
            'business_slug'        => $slug,
            'business_address'     => $request->business_address,
            'address_code'         => $request->address_code,
            'is_verified_business' => $isVerified,
            'verification_status'  => 'pending',
            'is_active'            => true,
            'is_approved'          => false,
            'referral_code'        => strtoupper(Str::random(8)),
            'referred_by'          => $referredBy,
            'marketer_id'          => $marketerId,   // ← NEW
        ]);

        // ── Document upload ───────────────────────────────────────────────────
        if ($isVerified && $request->hasFile('document_file')) {
            $uploaded = $cloudinary->uploadDocument(
                $request->file('document_file'),
                'orderer/seller-docs'
            );

            SellerDocument::create([
                'seller_id'            => $seller->id,
                'document_type'        => $request->document_type,
                'document_url'         => $uploaded['url'],
                'cloudinary_public_id' => $uploaded['public_id'],
                'original_filename'    => $request->file('document_file')->getClientOriginalName(),
                'status'               => 'pending',
            ]);
        }

        // ── Welcome email & login ─────────────────────────────────────────────
        $brevo->sendWelcomeSeller($seller);
        auth('seller')->login($seller);

        return redirect()->route('seller.pending')
            ->with('info', 'Your account has been created and is awaiting approval.');
    }
}