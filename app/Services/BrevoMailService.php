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
    /**
     * Send withdrawal success email to seller
     */
    public function sendWithdrawalSuccess($withdrawal): bool
    {
        $seller = $withdrawal->seller;
        $amount = number_format($withdrawal->amount, 2);
        $currency = $withdrawal->currency ?? 'USD';
        $localAmount = $withdrawal->converted_amount ? number_format($withdrawal->converted_amount, 2) : null;
        $exchangeRate = $withdrawal->exchange_rate ? number_format($withdrawal->exchange_rate, 4) : null;
        
        return $this->send(
            $seller->email,
            $seller->full_name,
            "Withdrawal of \${$amount} processed — Orderer",
            $this->withdrawalSuccessHtml($withdrawal, $seller, $amount, $currency, $localAmount, $exchangeRate)
        );
    }

    /**
     * Send withdrawal rejection email to seller
     */
    public function sendWithdrawalRejected($withdrawal, string $reason): bool
    {
        $seller = $withdrawal->seller;
        $amount = number_format($withdrawal->amount, 2);
        
        return $this->send(
            $seller->email,
            $seller->full_name,
            "Update on your withdrawal request — Orderer",
            $this->withdrawalRejectedHtml($withdrawal, $seller, $amount, $reason)
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

    /**
     * Withdrawal success email HTML template
     */
    protected function withdrawalSuccessHtml($withdrawal, $seller, $amount, $currency, $localAmount, $exchangeRate): string
    {
        $hasConversion = $localAmount && $exchangeRate && $currency !== 'USD';
        $reference = $withdrawal->korapay_reference ?? $withdrawal->transaction_reference ?? 'N/A';
        
        return "
        <div style='font-family:Arial,sans-serif;max-width:600px;margin:0 auto;'>
            <div style='background:#2ECC71;padding:30px;text-align:center;'>
                <h1 style='color:#fff;margin:0;'>Withdrawal Processed! 🎉</h1>
            </div>
            <div style='padding:30px;background:#fff;'>
                <p>Hi <strong>{$seller->first_name}</strong>,</p>
                <p>Your withdrawal request has been successfully processed and the funds have been sent to your bank account.</p>
                
                <div style='background:#f8f9fa;padding:20px;border-radius:8px;margin:20px 0;'>
                    <h3 style='margin:0 0 15px 0;color:#2ECC71;'>Withdrawal Details</h3>
                    <table style='width:100%;font-size:14px;'>
                         <tr>
                            <td style='padding:8px 0;color:#6c757d;'>Amount:</td>
                            <td style='padding:8px 0;font-weight:bold;'>\${$amount}</td>
                         </tr>
                         <tr>
                            <td style='padding:8px 0;color:#6c757d;'>Bank:</td>
                            <td style='padding:8px 0;'>{$withdrawal->bank_name}</td>
                         </tr>
                         <tr>
                            <td style='padding:8px 0;color:#6c757d;'>Account Number:</td>
                            <td style='padding:8px 0;'>{$withdrawal->account_number}</td>
                         </tr>
                         <tr>
                            <td style='padding:8px 0;color:#6c757d;'>Account Name:</td>
                            <td style='padding:8px 0;'>{$withdrawal->account_name}</td>
                         </tr>" . 
                        ($hasConversion ? "
                         <tr>
                            <td style='padding:8px 0;color:#6c757d;'>Exchange Rate:</td>
                            <td style='padding:8px 0;'>1 USD = {$exchangeRate} {$currency}</td>
                         </tr>
                         <tr>
                            <td style='padding:8px 0;color:#6c757d;'>Amount Received:</td>
                            <td style='padding:8px 0;font-weight:bold;color:#2ECC71;'>{$localAmount} {$currency}</td>
                         </tr>" : "") . "
                         <tr>
                            <td style='padding:8px 0;color:#6c757d;'>Reference:</td>
                            <td style='padding:8px 0;'><code>{$reference}</code></td>
                         </tr>
                         <tr>
                            <td style='padding:8px 0;color:#6c757d;'>Processed Date:</td>
                            <td style='padding:8px 0;'>{$withdrawal->processed_at->format('M d, Y H:i')}</td>
                         </tr>
                    </table>
                </div>
                
                <p>The funds should reflect in your account within <strong>1-3 business days</strong> depending on your bank.</p>
                
                <div style='text-align:center;margin:30px 0;'>
                    <a href='" . route('seller.withdrawals.index') . "' style='background:#2ECC71;color:#fff;padding:14px 28px;text-decoration:none;border-radius:4px;font-weight:bold;'>
                        View Withdrawal History
                    </a>
                </div>
                
                <p style='color:#888;font-size:13px;margin-top:24px;'>
                    Questions about your withdrawal? Contact us at support@orderer.com
                </p>
            </div>
            <div style='background:#f8f8f8;padding:16px;text-align:center;font-size:12px;color:#aaa;'>
                &copy; " . date('Y') . " Orderer. All rights reserved.
            </div>
        </div>";
    }
/**
 * Withdrawal rejection email HTML template
 */
protected function withdrawalRejectedHtml($withdrawal, $seller, $amount, $reason): string
{
    return "
    <div style='font-family:Arial,sans-serif;max-width:600px;margin:0 auto;'>
        <div style='background:#E74C3C;padding:30px;text-align:center;'>
            <h1 style='color:#fff;margin:0;'>Withdrawal Request Update</h1>
        </div>
        <div style='padding:30px;background:#fff;'>
            <p>Hi <strong>{$seller->first_name}</strong>,</p>
            <p>Your withdrawal request of <strong>\${$amount}</strong> has been reviewed and could not be processed at this time.</p>
            
            <div style='background:#FEF9E7;border-left:3px solid #F39C12;padding:12px 16px;margin:20px 0;font-size:14px;'>
                <strong>Reason:</strong> {$reason}
            </div>
            
            <div style='background:#f8f9fa;padding:20px;border-radius:8px;margin:20px 0;'>
                <h3 style='margin:0 0 15px 0;color:#E74C3C;'>Request Details</h3>
                <table style='width:100%;font-size:14px;'>
                    <tr>
                        <td style='padding:8px 0;color:#6c757d;'>Amount:</td>
                        <td style='padding:8px 0;font-weight:bold;'>\${$amount}</td>
                    </tr>
                    <tr>
                        <td style='padding:8px 0;color:#6c757d;'>Bank:</td>
                        <td style='padding:8px 0;'>{$withdrawal->bank_name}</td>
                    </tr>
                    <tr>
                        <td style='padding:8px 0;color:#6c757d;'>Account Number:</td>
                        <td style='padding:8px 0;'>{$withdrawal->account_number}</td>
                    </tr>
                    <tr>
                        <td style='padding:8px 0;color:#6c757d;'>Requested Date:</td>
                        <td style='padding:8px 0;'>{$withdrawal->created_at->format('M d, Y H:i')}</td>
                    </tr>
                </table>
            </div>
            
            <p>The funds have been <strong>returned to your wallet balance</strong>. You can request a new withdrawal after correcting the issues mentioned above.</p>
            
            <div style='text-align:center;margin:30px 0;'>
                <a href='" . route('seller.withdrawals.create') . "' style='background:#2ECC71;color:#fff;padding:14px 28px;text-decoration:none;border-radius:4px;font-weight:bold;'>
                    Request New Withdrawal
                </a>
            </div>
            
            <p style='color:#888;font-size:13px;margin-top:24px;'>
                If you have questions, please contact support@orderer.com
            </p>
        </div>
        <div style='background:#f8f8f8;padding:16px;text-align:center;font-size:12px;color:#aaa;'>
            &copy; " . date('Y') . " Orderer. All rights reserved.
        </div>
    </div>";
}
}