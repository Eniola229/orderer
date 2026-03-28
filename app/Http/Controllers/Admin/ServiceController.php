<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServiceListing;
use App\Models\Notification;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index(Request $request)
    {
        if (!auth('admin')->user()->canView()) abort(403);

        $query = ServiceListing::with(['seller', 'category']);

        if ($request->status && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->search) {
            $s = $request->search;
            $query->where(function($q) use ($s) {
                $q->where('title', 'like', "%{$s}%")
                  ->orWhereHas('seller', fn($r) => $r->where('business_name', 'like', "%{$s}%"));
            });
        }

        $services = $query->latest()->paginate(20)->withQueryString();

        return view('admin.services.index', compact('services'));
    }

    public function pending()
    {
        if (!auth('admin')->user()->canModerateSellers()) abort(403);

        $services = ServiceListing::where('status', 'pending')
            ->with(['seller', 'category'])
            ->latest()
            ->paginate(20);

        return view('admin.services.pending', compact('services'));
    }

    public function show(ServiceListing $service)
    {
        if (!auth('admin')->user()->canView()) abort(403);
        
        $service->load(['seller', 'category']);
        
        return view('admin.services.show', compact('service'));
    }

    public function approve(ServiceListing $service)
    {
        if (!auth('admin')->user()->canModerateSellers()) abort(403);

        if (!in_array($service->status, ['pending', 'suspended'])) {
            return back()->with('error', 'Only pending or suspended services can be approved.');
        }

        $service->update([
            'status' => 'approved',
            'approved_by' => auth('admin')->id(),
            'rejection_reason' => null,
        ]);

        Notification::create([
            'notifiable_type' => 'App\Models\Seller',
            'notifiable_id'   => $service->seller_id,
            'type'            => 'service_approved',
            'title'           => 'Service Approved',
            'body'            => "Your service \"{$service->title}\" has been approved and is now live.",
            'action_url'      => route('seller.services.index'),
        ]);

        return back()->with('success', "Service \"{$service->title}\" approved.");
    }

    public function reject(Request $request, ServiceListing $service)
    {
        if (!auth('admin')->user()->canModerateSellers()) abort(403);

        $request->validate([
            'reason' => ['required', 'string', 'max:500'],
        ]);

        $service->update([
            'status' => 'rejected',
            'rejection_reason' => $request->reason,
            'approved_by' => null,
        ]);

        Notification::create([
            'notifiable_type' => 'App\Models\Seller',
            'notifiable_id'   => $service->seller_id,
            'type'            => 'service_rejected',
            'title'           => 'Service Not Approved',
            'body'            => "Your service \"{$service->title}\" was not approved. Reason: {$request->reason}",
            'action_url'      => route('seller.services.index'),
        ]);

        return back()->with('success', 'Service rejected.');
    }

    public function suspend(ServiceListing $service)
    {
        if (!auth('admin')->user()->canModerateSellers()) abort(403);
        
        $service->update(['status' => 'suspended']);
        
        Notification::create([
            'notifiable_type' => 'App\Models\Seller',
            'notifiable_id'   => $service->seller_id,
            'type'            => 'service_suspended',
            'title'           => 'Service Suspended',
            'body'            => "Your service \"{$service->title}\" has been suspended.",
            'action_url'      => route('seller.services.index'),
        ]);
        
        return back()->with('success', 'Service suspended.');
    }
}