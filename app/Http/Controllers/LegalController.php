<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LegalController extends Controller
{
    public function terms()
    {
        return view('legal.terms-and-condition');
    }

    public function privacy()
    {
        return view('legal.privacy-policy');
    }

    public function refundPolicy()
    {
        return view('legal.refund-policy');
    }

    public function sellerTerms()
    {
        return view('legal.seller-terms');
    }

    public function buyerTerms()
    {
        return view('legal.buyer-terms');
    }

    public function shippingPolicy()
    {
        return view('legal.shipping-policy');
    }

    public function cookiePolicy()
    {
        return view('legal.cookie-policy');
    }

    public function amlPolicy()
    {
        return view('legal.aml-policy');
    }

    public function acceptableUse()
    {
        return view('legal.acceptable-use');
    }

    public function disclaimer()
    {
        return view('legal.disclaimer');
    }
}