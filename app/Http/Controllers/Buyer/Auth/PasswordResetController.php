<?php

namespace App\Http\Controllers\Buyer\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\BrevoMailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;

class PasswordResetController extends Controller
{
    protected BrevoMailService $mailer;

    public function __construct(BrevoMailService $mailer)
    {
        $this->mailer = $mailer;
    }

    // -------------------------------------------------------
    // Step 1 — Show "Forgot password" form
    // -------------------------------------------------------

    public function showForgotForm()
    {
        return view('buyer.auth.forgot-password');
    }

    // -------------------------------------------------------
    // Step 2 — Send reset link email
    // -------------------------------------------------------

    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $user = User::where('email', $request->email)->first();

        // Always show success to prevent user enumeration
        if (!$user) {
            return back()->with('status', 'If that email is registered, you will receive a reset link shortly.');
        }

        // Delete any existing token for this email
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        $token = Str::random(64);

        DB::table('password_reset_tokens')->insert([
            'email'      => $request->email,
            'token'      => Hash::make($token),
            'created_at' => Carbon::now(),
        ]);

        $resetUrl = route('password.reset', [
            'token' => $token,
            'email' => $request->email,
        ]);

        $this->mailer->sendPasswordReset(
            $user->email,
            $user->full_name ?? $user->first_name,
            $resetUrl,
            'buyer'
        );

        return back()->with('status', 'If that email is registered, you will receive a reset link shortly.');
    }

    // -------------------------------------------------------
    // Step 3 — Show "Set new password" form
    // -------------------------------------------------------

    public function showResetForm(Request $request, string $token)
    {
        return view('buyer.auth.reset-password', [
            'token' => $token,
            'email' => $request->query('email'),
        ]);
    }

    // -------------------------------------------------------
    // Step 4 — Update password
    // -------------------------------------------------------

    public function updatePassword(Request $request)
    {
        $request->validate([
            'token'                 => ['required'],
            'email'                 => ['required', 'email'],
            'password'              => ['required', 'min:8', 'confirmed'],
        ]);

        $record = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if (!$record || !Hash::check($request->token, $record->token)) {
            return back()->withErrors(['email' => 'This password reset link is invalid or has expired.']);
        }

        // Expire tokens older than 60 minutes
        if (Carbon::parse($record->created_at)->addMinutes(60)->isPast()) {
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();
            return back()->withErrors(['email' => 'This password reset link has expired. Please request a new one.']);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors(['email' => 'No account found with that email address.']);
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        // Clean up the used token
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return redirect()->route('login')->with('status', 'Your password has been reset successfully. Please sign in.');
    }
}