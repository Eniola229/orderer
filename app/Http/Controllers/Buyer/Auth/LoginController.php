<?php

namespace App\Http\Controllers\Buyer\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    public function showForm()
    {
        return view('buyer.auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Capture guest session ID BEFORE auth changes anything
        $oldSessionId = session()->getId();

        $credentials = $request->only('email', 'password');
        $remember    = $request->boolean('remember');

        if (!auth('web')->attempt($credentials, $remember)) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'These credentials do not match our records.']);
        }

        $user = auth('web')->user();

        if (!$user->is_active) {
            auth('web')->logout();
            return back()->withErrors(['email' => 'Your account has been suspended. Contact support.']);
        }

        $user->update([
            'last_login_at' => now(),
            'last_login_ip' => $request->ip(),
        ]);

        $request->session()->regenerate();

        // Merge using the OLD session ID, before it was regenerated
        app(\App\Http\Controllers\CartController::class)->mergeGuestCart($oldSessionId);

        return redirect()->intended(route('home'))
            ->with('success', "Welcome back, {$user->first_name}!");
    }
    public function logout(Request $request)
    {
        auth('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')
            ->with('success', 'You have been signed out.');
    }
}