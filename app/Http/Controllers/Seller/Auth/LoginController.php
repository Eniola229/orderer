<?php

namespace App\Http\Controllers\Seller\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    public function showForm()
    {
        return view('seller.auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        $credentials = $request->only('email', 'password');
        $remember    = $request->boolean('remember');

        if (!auth('seller')->attempt($credentials, $remember)) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'These credentials do not match our records.']);
        }

        $seller = auth('seller')->user();

        if (!$seller->is_active) {
            auth('seller')->logout();
            return back()->withErrors([
                'email' => 'Your seller account has been suspended. Contact support@orderer.com'
            ]);
        }

        // Update last login
        $seller->update([
            'last_login_at' => now(),
            'last_login_ip' => $request->ip(),
        ]);

        $request->session()->regenerate();

        // If not approved yet send to pending page
        if (!$seller->is_approved) {
            return redirect()->route('seller.pending');
        }

        return redirect()->route('seller.dashboard')
            ->with('success', "Welcome back, {$seller->first_name}!");
    }

    public function logout(Request $request)
    {
        auth('seller')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('seller.login')
            ->with('success', 'You have been signed out.');
    }
}