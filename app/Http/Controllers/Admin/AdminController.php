<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function index()
    {
        if (!auth('admin')->user()->canManageAdmins()) abort(403);

        $admins = Admin::latest()->paginate(20);

        return view('admin.admins.index', compact('admins'));
    }

    public function create()
    {
        if (!auth('admin')->user()->canManageAdmins()) abort(403);

        return view('admin.admins.create');
    }

    public function store(Request $request)
    {
        if (!auth('admin')->user()->canManageAdmins()) abort(403);

        $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name'  => ['required', 'string', 'max:100'],
            'email'      => ['required', 'email', 'unique:admins,email'],
            'password'   => ['required', 'confirmed', 'min:8'],
            'role'       => ['required', 'in:' . implode(',', array_keys(Admin::roles()))],
            'is_active'  => ['required', 'boolean'],
        ]);

        Admin::create([
            'first_name' => $request->first_name,
            'last_name'  => $request->last_name,
            'email'      => $request->email,
            'password'   => Hash::make($request->password),
            'role'       => $request->role,
            'is_active'  => $request->is_active,
        ]);

        return redirect()->route('admin.admins.index')
            ->with('success', 'Admin account created.');
    }

    public function edit(Admin $adminUser)
    {
        if (!auth('admin')->user()->canManageAdmins()) abort(403);

        // Super admin cannot edit another super admin unless they are one
        if ($adminUser->isSuperAdmin() && !auth('admin')->user()->isSuperAdmin()) {
            abort(403);
        }

        return view('admin.admins.edit', ['adminUser' => $adminUser]);
    }

    public function update(Request $request, Admin $adminUser)
    {
        if (!auth('admin')->user()->canManageAdmins()) abort(403);

        if ($adminUser->isSuperAdmin() && !auth('admin')->user()->isSuperAdmin()) {
            abort(403);
        }

        $request->validate([
            'role'      => ['required', 'in:' . implode(',', array_keys(Admin::roles()))],
            'is_active' => ['required', 'boolean'],
        ]);

        $adminUser->update($request->only('role', 'is_active'));

        return redirect()->route('admin.admins.index')
            ->with('success', 'Admin updated.');
    }

    public function suspend(Admin $adminUser)
    {
        if (!auth('admin')->user()->canManageAdmins()) abort(403);
        
        if ($adminUser->id === auth('admin')->id()) {
            return back()->with('error', 'You cannot suspend yourself.');
        }
        
        $adminUser->update(['is_active' => false]);
        
        return back()->with('success', 'Admin suspended.');
    }
    
    public function activate(Admin $adminUser)
    {
        if (!auth('admin')->user()->canManageAdmins()) abort(403);
        
        if ($adminUser->id === auth('admin')->id()) {
            return back()->with('error', 'You cannot activate yourself.');
        }
        
        $adminUser->update(['is_active' => true]);
        
        return back()->with('success', 'Admin activated.');
    }
}