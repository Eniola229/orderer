<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HouseListing;
use App\Models\Notification;
use Illuminate\Http\Request;

class HouseController extends Controller
{
    public function index(Request $request)
    {
        if (!auth('admin')->user()->canView()) abort(403);

        $query = HouseListing::with(['seller', 'images']);

        if ($request->status && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->type) {
            $query->where('listing_type', $request->type);
        }

        if ($request->property_type) {
            $query->where('property_type', $request->property_type);
        }

        if ($request->search) {
            $s = $request->search;
            $query->where(function($q) use ($s) {
                $q->where('title', 'like', "%{$s}%")
                  ->orWhere('location', 'like', "%{$s}%")
                  ->orWhereHas('seller', fn($r) => $r->where('business_name', 'like', "%{$s}%"));
            });
        }

        $houses = $query->latest()->paginate(20)->withQueryString();

        return view('admin.houses.index', compact('houses'));
    }

    public function pending()
    {
        if (!auth('admin')->user()->canModerateSellers()) abort(403);

        $houses = HouseListing::where('status', 'pending')
            ->with(['seller', 'images'])
            ->latest()
            ->paginate(20);

        return view('admin.houses.pending', compact('houses'));
    }

    public function show(HouseListing $house)
    {
        if (!auth('admin')->user()->canView()) abort(403);
        
        $house->load(['seller', 'images']);
        
        return view('admin.houses.show', compact('house'));
    }

    public function approve(HouseListing $house)
    {
        if (!auth('admin')->user()->canModerateSellers()) abort(403);

        if (!in_array($house->status, ['pending', 'suspended'])) { 
            return back()->with('error', 'Only pending or suspended properties can be approved.');
        }

        $house->update([
            'status' => 'approved',
            'approved_by' => auth('admin')->id(),
            'rejection_reason' => null,
        ]);

        Notification::create([
            'notifiable_type' => 'App\Models\Seller',
            'notifiable_id'   => $house->seller_id,
            'type'            => 'property_approved',
            'title'           => 'Property Approved',
            'body'            => "Your property listing \"{$house->title}\" has been approved and is now live.",
            'action_url'      => route('seller.houses.index'),
        ]);

        return back()->with('success', "Property \"{$house->title}\" approved.");
    }

    public function reject(Request $request, HouseListing $house)
    {
        if (!auth('admin')->user()->canModerateSellers()) abort(403);

        $request->validate([
            'reason' => ['required', 'string', 'max:500'],
        ]);

        $house->update([
            'status' => 'rejected',
            'rejection_reason' => $request->reason,
            'approved_by' => null,
        ]);

        Notification::create([
            'notifiable_type' => 'App\Models\Seller',
            'notifiable_id'   => $house->seller_id,
            'type'            => 'property_rejected',
            'title'           => 'Property Not Approved',
            'body'            => "Your property listing \"{$house->title}\" was not approved. Reason: {$request->reason}",
            'action_url'      => route('seller.houses.index'),
        ]);

        return back()->with('success', 'Property rejected.');
    }

    public function suspend(HouseListing $house)
    {
        if (!auth('admin')->user()->canModerateSellers()) abort(403);
        
        $house->update(['status' => 'suspended']);
        
        Notification::create([
            'notifiable_type' => 'App\Models\Seller',
            'notifiable_id'   => $house->seller_id,
            'type'            => 'property_suspended',
            'title'           => 'Property Suspended',
            'body'            => "Your property listing \"{$house->title}\" has been suspended.",
            'action_url'      => route('seller.houses.index'),
        ]);
        
        return back()->with('success', 'Property suspended.');
    }
}