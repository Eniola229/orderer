<?php
namespace App\Http\Controllers\Seller\Auth;

use App\Http\Controllers\Controller;
use App\Services\TermiiService;
use Illuminate\Http\Request;

class PhoneVerificationController extends Controller
{
    public function __construct(protected TermiiService $termii) {}

    // Show the "enter your code" page, auto-sending an OTP if none is pending
    public function notice()
    {
        $seller = auth('seller')->user();

        if ($seller->phone_verified_at) {
            return redirect()->route('seller.dashboard');
        }

        if (!$seller->phone) {
            return redirect()->route('seller.dashboard')
                ->with('error', 'Please add a phone number to your profile first.');
        }

        if (!session('phone_verification_pin_id')) {
            $this->sendOtp($seller);
        }

        return view('seller.auth.verify-phone', ['phone' => $seller->phone]);
    }

    // Resend button
    public function send()
    {
        $seller = auth('seller')->user();

        if ($seller->phone_verified_at) {
            return back()->with('info', 'Your phone is already verified.');
        }

        $result = $this->sendOtp($seller);

        if (!$result['success']) {
            return back()->with('error', $result['message']);
        }

        return back()->with('success', 'Verification code sent to ' . $seller->phone);
    }

    // Submit the code
    public function verify(Request $request)
    {
        $request->validate([
            'code' => ['required', 'string', 'min:4', 'max:8'],
        ]);

        $seller = auth('seller')->user();
        $pinId  = session('phone_verification_pin_id');

        if (!$pinId) {
            return back()->with('error', 'Verification session expired. Please request a new code.');
        }

        $result = $this->termii->verifyOtp($pinId, $request->code);

        if (!$result['success']) {
            return back()->with('error', $result['message'] ?? 'Invalid or expired code.');
        }

        $seller->update(['phone_verified_at' => now()]);
        session()->forget('phone_verification_pin_id');

        return redirect()->route('seller.dashboard')->with('success', 'Phone number verified successfully!');
    }

    protected function sendOtp($seller): array
    {
        $result = $this->termii->sendOtp($seller->phone);

        if ($result['success']) {
            session(['phone_verification_pin_id' => $result['pin_id']]);
        }

        return $result;
    }

    public function updatePhone(Request $request)
    {
        $seller = auth('seller')->user();

        $request->validate([
            'phone' => [
                'required',
                'string',
                'regex:/^\+234[0-9]{10}$/',
                'unique:sellers,phone,' . $seller->id,
            ],
        ], [
            'phone.regex' => 'Phone number must be in the format +234XXXXXXXXXX.',
        ]);

        $seller->update([
            'phone'             => $request->phone,
            'phone_verified_at' => null,
        ]);

        session()->forget('phone_verification_pin_id');

        $result = $this->sendOtp($seller);

        if (!$result['success']) {
            return redirect()->route('seller.phone-verification.notice')
                ->with('error', 'Phone updated, but we could not send a code: ' . $result['message']);
        }

        return redirect()->route('seller.phone-verification.notice')
            ->with('success', 'Phone updated! We sent a new code to ' . $request->phone);
    }
}