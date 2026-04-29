<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Marketer;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;

class MarketerController extends Controller
{
    public function index()
    {
        // Only super_admin and HR can manage marketers
        if (!auth('admin')->user()->canManageAdmins()) abort(403);

        $marketers = Marketer::withCount('referredSellers')->latest()->paginate(20);

        return view('admin.marketers.index', compact('marketers'));
    }

    public function create()
    {
        if (!auth('admin')->user()->canManageAdmins()) abort(403);

        return view('admin.marketers.create');
    }

    public function store(Request $request)
    {
        if (!auth('admin')->user()->canManageAdmins()) abort(403);

        $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name'  => ['required', 'string', 'max:100'],
            'email'      => ['required', 'email', 'unique:marketers,email'],
            'password'   => ['required', 'confirmed', Password::min(8)],
            'notes'      => ['nullable', 'string', 'max:500'],
            'is_active'  => ['required', 'in:0,1'],
        ]);

        $marketer = Marketer::create([
            'first_name'      => $request->first_name,
            'last_name'       => $request->last_name,
            'email'           => $request->email,
            'password'        => $request->password,
            'marketing_code'  => Marketer::generateMarketingCode(),
            'is_active'       => $request->is_active,
            'notes'           => $request->notes,
        ]);

        return redirect()->route('admin.marketers.index')
            ->with('success', "Marketer account created. Code: {$marketer->marketing_code}");
    }

    public function show(Request $request, Marketer $marketer)
    {
        if (!auth('admin')->user()->canManageAdmins()) abort(403);

        // Get filter parameters
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');
        $verificationFilter = $request->get('verification_filter');
        $statusFilter = $request->get('status_filter');
        $sellerSearch = $request->get('seller_search');
        
        // Build the query for sellers
        $sellersQuery = $marketer->referredSellers();
        
        // Apply custom date range filter (from - to)
        if ($dateFrom) {
            $sellersQuery->whereDate('created_at', '>=', $dateFrom);
        }
        if ($dateTo) {
            $sellersQuery->whereDate('created_at', '<=', $dateTo);
        }
        
        // Apply verification filter
        if ($verificationFilter && $verificationFilter !== 'all') {
            $sellersQuery->where('verification_status', $verificationFilter);
        }
        
        // Apply status filter (approved/pending)
        if ($statusFilter && $statusFilter !== 'all') {
            $sellersQuery->where('is_approved', $statusFilter === 'approved');
        }
        
        // Apply seller search (search by name, email, or business name)
        if ($sellerSearch) {
            $sellersQuery->where(function($q) use ($sellerSearch) {
                $q->where('first_name', 'like', "%{$sellerSearch}%")
                  ->orWhere('last_name', 'like', "%{$sellerSearch}%")
                  ->orWhere('email', 'like', "%{$sellerSearch}%")
                  ->orWhere('business_name', 'like', "%{$sellerSearch}%");
            });
        }
        
        // Get filtered sellers
        $sellers = $sellersQuery->get();
        
        // Calculate stats based on original data (unfiltered)
        $stats = [
            'total'    => $marketer->referredSellers()->count(),
            'approved' => $marketer->referredSellers()->where('is_approved', true)->count(),
            'pending'  => $marketer->referredSellers()->where('is_approved', false)->count(),
            'verified'  => $marketer->referredSellers()->where('verification_status', 'approved')->count(),
            'unverified' => $marketer->referredSellers()->where('verification_status', '!=', 'approved')->count(),
        ];
        
        // Pass filter values back to view for persistence
        return view('admin.marketers.show', compact('marketer', 'sellers', 'stats', 
            'dateFrom', 'dateTo', 'verificationFilter', 'statusFilter', 'sellerSearch'));
    }

    public function edit(Marketer $marketer)
    {
        if (!auth('admin')->user()->canManageAdmins()) abort(403);

        return view('admin.marketers.edit', compact('marketer'));
    }

    public function update(Request $request, Marketer $marketer)
    {
        if (!auth('admin')->user()->canManageAdmins()) abort(403);

        $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name'  => ['required', 'string', 'max:100'],
            'email'      => ['required', 'email', 'unique:marketers,email,' . $marketer->id],
            'notes'      => ['nullable', 'string', 'max:500'],
            'is_active'  => ['required', 'in:0,1'],
            'password'   => ['nullable', 'confirmed', Password::min(8)],
        ]);

        $data = [
            'first_name' => $request->first_name,
            'last_name'  => $request->last_name,
            'email'      => $request->email,
            'notes'      => $request->notes,
            'is_active'  => $request->is_active,
        ];

        if ($request->filled('password')) {
            $data['password'] = $request->password;
        }

        $marketer->update($data);

        return back()->with('success', 'Marketer updated successfully.');
    }

    public function suspend(Marketer $marketer)
    {
        if (!auth('admin')->user()->canManageAdmins()) abort(403);

        $marketer->update(['is_active' => false]);

        return back()->with('success', 'Marketer suspended.');
    }

    public function activate(Marketer $marketer)
    {
        if (!auth('admin')->user()->canManageAdmins()) abort(403);

        $marketer->update(['is_active' => true]);

        return back()->with('success', 'Marketer activated.');
    }

    public function regenerateCode(Marketer $marketer)
    {
        if (!auth('admin')->user()->canManageAdmins()) abort(403);

        $marketer->update([
            'marketing_code' => Marketer::generateMarketingCode(),
        ]);

        return back()->with('success', "New code generated: {$marketer->marketing_code}");
    }
}