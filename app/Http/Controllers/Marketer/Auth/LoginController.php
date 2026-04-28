<?php

namespace App\Http\Controllers\Marketer\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    public function showForm()
    {
        return view('marketer.auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (auth('marketer')->attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            auth('marketer')->user()->update(['last_login_at' => now()]);

            return redirect()->route('marketer.dashboard');
        }

        return back()->withErrors([
            'email' => 'These credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        auth('marketer')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('marketer.login');
    }
}