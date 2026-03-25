<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

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

        if (!auth('admin')->attempt($credentials)) {
            // Log failed attempt with IP
            \Log::warning('Failed admin login attempt', [
                'email' => $request->email,
                'ip'    => $request->ip(),
                'time'  => now(),
            ]);

            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'Invalid admin credentials.']);
        }

        $admin = auth('admin')->user();

        if (!$admin->is_active) {
            auth('admin')->logout();
            return back()->withErrors(['email' => 'This admin account has been disabled.']);
        }

        $admin->update([
            'last_login_at' => now(),
            'last_login_ip' => $request->ip(),
        ]);

        $request->session()->regenerate();

        return redirect()->route('admin.dashboard');
    }

    public function logout(Request $request)
    {
        auth('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}