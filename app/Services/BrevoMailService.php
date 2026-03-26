<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class BrevoMailService
{
    protected string $apiKey;
    protected string $fromEmail;
    protected string $fromName;

    public function __construct()
    {
        $this->apiKey    = config('services.brevo.api_key');
        $this->fromEmail = config('mail.from.address');
        $this->fromName  = config('mail.from.name');
    }

    public function send(string $toEmail, string $toName, string $subject, string $htmlContent): bool
    {
        $response = Http::withHeaders([
            'api-key'      => $this->apiKey,
            'Content-Type' => 'application/json',
        ])->post('https://api.brevo.com/v3/smtp/email', [
            'sender'      => ['email' => $this->fromEmail, 'name' => $this->fromName],
            'to'          => [['email' => $toEmail, 'name' => $toName]],
            'subject'     => $subject,
            'htmlContent' => $htmlContent,
        ]);

        return $response->successful();
    }

    public function sendWelcomeBuyer($user): bool
    {
        return $this->send(
            $user->email,
            $user->full_name,
            'Welcome to Orderer!',
            $this->welcomeBuyerHtml($user)
        );
    }

    public function sendWelcomeSeller($seller): bool
    {
        return $this->send(
            $seller->email,
            $seller->full_name,
            'Your Orderer seller account is under review',
            $this->welcomeSellerHtml($seller)
        );
    }

    public function sendSellerApproved($seller): bool
    {
        return $this->send(
            $seller->email,
            $seller->full_name,
            'Congratulations — Your Orderer seller account is approved!',
            $this->sellerApprovedHtml($seller)
        );
    }

    public function sendSellerRejected($seller, string $reason): bool
    {
        return $this->send(
            $seller->email,
            $seller->full_name,
            'Update on your Orderer seller application',
            $this->sellerRejectedHtml($seller, $reason)
        );
    }

    public function sendOrderPlacedBuyer($user, $order): bool
    {
        return $this->send(
            $user->email,
            $user->full_name,
            "Order #{$order->order_number} confirmed — Orderer",
            $this->orderConfirmationHtml($user, $order)
        );
    }

    public function sendOrderNotifySeller($seller, $order): bool
    {
        return $this->send(
            $seller->email,
            $seller->full_name,
            "New order #{$order->order_number} received — Orderer",
            $this->newOrderSellerHtml($seller, $order)
        );
    }

    public function sendPasswordReset(string $email, string $name, string $resetUrl, string $guard = 'buyer'): bool
    {
        return $this->send(
            $email,
            $name,
            'Reset your Orderer password',
            $this->passwordResetHtml($name, $resetUrl)
        );
    }

    // -------------------------------------------------------
    // Email HTML templates
    // -------------------------------------------------------

    protected function welcomeBuyerHtml($user): string
    {
        return "
        <div style='font-family:Arial,sans-serif;max-width:600px;margin:0 auto;'>
            <div style='background:#2ECC71;padding:30px;text-align:center;'>
                <h1 style='color:#fff;margin:0;'>Welcome to Orderer!</h1>
            </div>
            <div style='padding:30px;background:#fff;'>
                <p>Hi <strong>{$user->first_name}</strong>,</p>
                <p>Your account has been created successfully. You can now shop, book riders and track all your orders from one place.</p>
                <div style='text-align:center;margin:30px 0;'>
                    <a href='" . route('home') . "' style='background:#2ECC71;color:#fff;padding:14px 28px;text-decoration:none;border-radius:4px;font-weight:bold;'>
                        Start Shopping
                    </a>
                </div>
                <p style='color:#888;font-size:13px;'>
                    Your referral code is: <strong>{$user->referral_code}</strong><br>
                    Share it with friends and earn when they shop!
                </p>
            </div>
            <div style='background:#f8f8f8;padding:16px;text-align:center;font-size:12px;color:#aaa;'>
                &copy; " . date('Y') . " Orderer. All rights reserved.
            </div>
        </div>";
    }

    protected function welcomeSellerHtml($seller): string
    {
        return "
        <div style='font-family:Arial,sans-serif;max-width:600px;margin:0 auto;'>
            <div style='background:#2ECC71;padding:30px;text-align:center;'>
                <h1 style='color:#fff;margin:0;'>Seller Application Received</h1>
            </div>
            <div style='padding:30px;background:#fff;'>
                <p>Hi <strong>{$seller->first_name}</strong>,</p>
                <p>Thank you for applying to sell on Orderer. Your application for <strong>{$seller->business_name}</strong> is currently under review.</p>
                <p>Our team will review your details within <strong>24 hours</strong> and notify you via email once approved.</p>
                <p style='color:#888;font-size:13px;margin-top:24px;'>
                    Questions? Email us at support@orderer.com
                </p>
            </div>
            <div style='background:#f8f8f8;padding:16px;text-align:center;font-size:12px;color:#aaa;'>
                &copy; " . date('Y') . " Orderer. All rights reserved.
            </div>
        </div>";
    }

    protected function sellerApprovedHtml($seller): string
    {
        return "
        <div style='font-family:Arial,sans-serif;max-width:600px;margin:0 auto;'>
            <div style='background:#2ECC71;padding:30px;text-align:center;'>
                <h1 style='color:#fff;margin:0;'>You're approved!</h1>
            </div>
            <div style='padding:30px;background:#fff;'>
                <p>Hi <strong>{$seller->first_name}</strong>,</p>
                <p>Great news! Your seller account for <strong>{$seller->business_name}</strong> has been approved. You can now start listing products, services and properties on Orderer.</p>
                <div style='text-align:center;margin:30px 0;'>
                    <a href='" . route('seller.dashboard') . "' style='background:#2ECC71;color:#fff;padding:14px 28px;text-decoration:none;border-radius:4px;font-weight:bold;'>
                        Go to Dashboard
                    </a>
                </div>
            </div>
            <div style='background:#f8f8f8;padding:16px;text-align:center;font-size:12px;color:#aaa;'>
                &copy; " . date('Y') . " Orderer. All rights reserved.
            </div>
        </div>";
    }

    protected function sellerRejectedHtml($seller, string $reason): string
    {
        return "
        <div style='font-family:Arial,sans-serif;max-width:600px;margin:0 auto;'>
            <div style='background:#E74C3C;padding:30px;text-align:center;'>
                <h1 style='color:#fff;margin:0;'>Application Update</h1>
            </div>
            <div style='padding:30px;background:#fff;'>
                <p>Hi <strong>{$seller->first_name}</strong>,</p>
                <p>After reviewing your seller application for <strong>{$seller->business_name}</strong>, we were unable to approve it at this time.</p>
                <div style='background:#FEF9E7;border-left:3px solid #F39C12;padding:12px 16px;margin:16px 0;font-size:14px;'>
                    <strong>Reason:</strong> {$reason}
                </div>
                <p>You can re-apply or upload updated documents from your dashboard. If you have questions, contact support@orderer.com.</p>
            </div>
            <div style='background:#f8f8f8;padding:16px;text-align:center;font-size:12px;color:#aaa;'>
                &copy; " . date('Y') . " Orderer. All rights reserved.
            </div>
        </div>";
    }

    protected function orderConfirmationHtml($user, $order): string
    {
        return "
        <div style='font-family:Arial,sans-serif;max-width:600px;margin:0 auto;'>
            <div style='background:#2ECC71;padding:30px;text-align:center;'>
                <h1 style='color:#fff;margin:0;'>Order Confirmed!</h1>
            </div>
            <div style='padding:30px;background:#fff;'>
                <p>Hi <strong>{$user->first_name}</strong>,</p>
                <p>Your order <strong>#{$order->order_number}</strong> has been placed successfully.</p>
                <p>Total: <strong>\${$order->total}</strong></p>
                <div style='text-align:center;margin:30px 0;'>
                    <a href='" . route('buyer.orders') . "' style='background:#2ECC71;color:#fff;padding:14px 28px;text-decoration:none;border-radius:4px;font-weight:bold;'>
                        Track Order
                    </a>
                </div>
            </div>
            <div style='background:#f8f8f8;padding:16px;text-align:center;font-size:12px;color:#aaa;'>
                &copy; " . date('Y') . " Orderer. All rights reserved.
            </div>
        </div>";
    }

    protected function newOrderSellerHtml($seller, $order): string
    {
        return "
        <div style='font-family:Arial,sans-serif;max-width:600px;margin:0 auto;'>
            <div style='background:#2ECC71;padding:30px;text-align:center;'>
                <h1 style='color:#fff;margin:0;'>New Order Received!</h1>
            </div>
            <div style='padding:30px;background:#fff;'>
                <p>Hi <strong>{$seller->first_name}</strong>,</p>
                <p>You have a new order <strong>#{$order->order_number}</strong> waiting for you.</p>
                <div style='text-align:center;margin:30px 0;'>
                    <a href='" . route('seller.orders') . "' style='background:#2ECC71;color:#fff;padding:14px 28px;text-decoration:none;border-radius:4px;font-weight:bold;'>
                        View Order
                    </a>
                </div>
            </div>
            <div style='background:#f8f8f8;padding:16px;text-align:center;font-size:12px;color:#aaa;'>
                &copy; " . date('Y') . " Orderer. All rights reserved.
            </div>
        </div>";
    }

    protected function passwordResetHtml(string $name, string $resetUrl): string
    {
        return "
        <div style='font-family:Arial,sans-serif;max-width:600px;margin:0 auto;'>
            <div style='background:#2ECC71;padding:30px;text-align:center;'>
                <h1 style='color:#fff;margin:0;'>Password Reset</h1>
            </div>
            <div style='padding:30px;background:#fff;'>
                <p>Hi <strong>{$name}</strong>,</p>
                <p>We received a request to reset your password. Click the button below. This link expires in 60 minutes.</p>
                <div style='text-align:center;margin:30px 0;'>
                    <a href='{$resetUrl}' style='background:#2ECC71;color:#fff;padding:14px 28px;text-decoration:none;border-radius:4px;font-weight:bold;'>
                        Reset Password
                    </a>
                </div>
                <p style='color:#888;font-size:13px;'>
                    If you did not request a password reset, ignore this email.
                </p>
            </div>
            <div style='background:#f8f8f8;padding:16px;text-align:center;font-size:12px;color:#aaa;'>
                &copy; " . date('Y') . " Orderer. All rights reserved.
            </div>
        </div>";
    }
}