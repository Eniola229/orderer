<?php

namespace App\Http\Controllers;

use App\Services\BrevoMailService;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function send(Request $request, BrevoMailService $brevo)
    {
        $request->validate([
            'name'    => ['required', 'string', 'max:200'],
            'email'   => ['required', 'email'],
            'subject' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string', 'min:10'],
        ]);

        // Send email to support
        $html = "
        <div style='font-family:Arial,sans-serif;max-width:600px;'>
            <h3>New Contact Form Submission</h3>
            <p><strong>From:</strong> {$request->name} ({$request->email})</p>
            <p><strong>Subject:</strong> {$request->subject}</p>
            <div style='background:#f8f8f8;padding:16px;border-radius:6px;'>
                <p style='margin:0;'>{$request->message}</p>
            </div>
        </div>";

        // Use Brevo send method directly
        $brevo->send(
            config('mail.from.address'),
            'Orderer Support',
            "Contact Form: {$request->subject}",
            $html
        );

        return back()->with('success', 'Message sent! We will get back to you within 24 hours.');
    }
}
