<?php

namespace App\Http\Controllers\Seller\Auth;

use App\Http\Controllers\Controller;
use App\Models\Seller;
use App\Services\BrevoMailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class PasswordResetController extends Controller
{
    public function showForgotForm()
    {
        return view('seller.auth.forgot-password');
    }

    public function sendResetLink(Request $request, BrevoMailService $brevo)
    {
        $request->validate([
            'email' => ['required', 'email', 'exists:sellers,email'],
        ]);

        $token = Str::random(64);

        DB::table('seller_password_reset_tokens')->upsert(
            ['email' => $request->email, 'token' => Hash::make($token), 'created_at' => now()],
            ['email'],
            ['token', 'created_at']
        );

        $seller   = Seller::where('email', $request->email)->first();
        $resetUrl = route('seller.password.reset', ['token' => $token, 'email' => $request->email]);

        $brevo->sendPasswordReset($seller->email, $seller->full_name, $resetUrl, 'seller');

        return back()->with('status', 'Password reset link sent to your email.');
    }

    public function showResetForm(Request $request, string $token)
    {
        return view('seller.auth.reset-password', [
            'token' => $token,
            'email' => $request->email,
        ]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token'    => ['required'],
            'email'    => ['required', 'email', 'exists:sellers,email'],
            'password' => ['required', 'confirmed', \Illuminate\Validation\Rules\Password::min(8)],
        ]);

        $record = DB::table('seller_password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if (!$record || !Hash::check($request->token, $record->token)) {
            return back()->withErrors(['email' => 'Invalid or expired reset link.']);
        }

        // Check expiry (60 min)
        if (now()->diffInMinutes($record->created_at) > 60) {
            DB::table('seller_password_reset_tokens')->where('email', $request->email)->delete();
            return back()->withErrors(['email' => 'Reset link has expired. Request a new one.']);
        }

        Seller::where('email', $request->email)->update([
            'password' => Hash::make($request->password),
        ]);

        DB::table('seller_password_reset_tokens')->where('email', $request->email)->delete();

        return redirect()->route('seller.login')
            ->with('success', 'Password reset successfully. Please sign in.');
    }
}