<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showForm()
    {
        return view('admin.auth.login');
    } 

    public function login(Request $request)
    {
        $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        $credentials = $request->only('email', 'password');
        $remember    = $request->boolean('remember');

        if (Auth::guard('admin')->attempt($credentials, $remember)) {
            $admin = Auth::guard('admin')->user();

            // Check if admin account exists and is active
            if (!$admin) {
                Auth::guard('admin')->logout();
                return back()->withErrors(['email' => 'Account not found.']);
            }

            // Check if account is active
            if (!$admin->is_active) {
                Auth::guard('admin')->logout();
                return back()->withErrors(['email' => 'Your admin account is inactive. Please contact a Super Admin.']);
            }

            // Optional: Check if email is verified if you have that field
            // if (!$admin->email_verified_at) {
            //     Auth::guard('admin')->logout();
            //     return back()->withErrors(['email' => 'Please verify your email address first.']);
            // }

            // Update last login timestamp
            $admin->update(['last_login_at' => now()]);

            try {
                app(\App\Services\BrevoMailService::class)
                    ->sendLoginNotification($admin, $request->ip(), $request->userAgent(), 'admin');
            } catch (\Exception $e) {
                \Log::error('Login email failed (admin): ' . $e->getMessage());
            }

            $request->session()->regenerate();

            return redirect()->route('admin.dashboard');
        }

        return back()->withErrors(['email' => 'Invalid credentials.']);
    }

    public function logout(Request $request)
    {
        $admin = Auth::guard('admin')->user();
        
        // Log the logout activity
        // if ($admin) {
        //     \App\Models\ActivityLog::create([
        //         'admin_id' => $admin->id,
        //         'action' => 'logout',
        //         'ip_address' => $request->ip(),
        //         'user_agent' => $request->userAgent(),
        //     ]);
        // }

        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('admin.login');
    }
}