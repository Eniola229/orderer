<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Disclaimer — Orderer</title>
    <link rel="icon" href="{{ asset('img/core-img/favicon.png') }}">
    <link rel="stylesheet" href="{{ asset('css/core-style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/orderer.css') }}">
    <style>
        .legal-wrap { max-width: 860px; margin: 0 auto; padding: 60px 24px 80px; }
        .legal-wrap h1 { font-size: 32px; font-weight: 800; color: #1a1a1a; margin-bottom: 6px; }
        .legal-wrap .meta { color: #888; font-size: 14px; margin-bottom: 40px; }
        .legal-wrap h2 { font-size: 20px; font-weight: 700; color: #1a1a1a; margin: 36px 0 12px; }
        .legal-wrap p, .legal-wrap li { font-size: 15px; color: #444; line-height: 1.85; }
        .legal-wrap ul { padding-left: 22px; margin-bottom: 16px; }
        .legal-wrap li { margin-bottom: 6px; }
        .legal-wrap a { color: #2ECC71; }
        .highlight-box { background: #EBF5FB; border-radius: 8px; padding: 16px 20px; margin: 20px 0; border-left: 3px solid #2980B9; }
        .highlight-box p { margin: 0; font-size: 14px; color: #1A5276; font-weight: 600; }
        hr { border-color: #eee; margin: 36px 0; }
    </style>
</head>
<body>

@auth('web')@include('layouts.storefront.header-auth')@else @include('layouts.storefront.header-guest')@endauth
@include('layouts.storefront.cart-sidebar')
@include('layouts.partials.alerts')

<div class="breadcumb_area bg-img" style="background-image:url({{ asset('img/bg-img/breadcumb.jpeg') }});">
    <div class="container h-100"><div class="row h-100 align-items-center"><div class="col-12">
        <div class="page-title text-center"><h2>Disclaimer</h2></div>
    </div></div></div>
</div>

<div class="legal-wrap">

    <h1>Disclaimer</h1>
    <p class="meta">Last updated: {{ date('F d, Y') }}</p>

    <div class="highlight-box">
        <p>ℹ Please read this Disclaimer carefully before using the Orderer Platform. By accessing or using Orderer, you acknowledge and agree to the terms set out below.</p>
    </div>

    <h2>1. Marketplace Facilitation Only</h2>
    <p>Orderer is an online marketplace platform that connects independent buyers and sellers. Orderer is not a manufacturer, distributor, retailer, or supplier of any of the goods, services, or properties listed on the Platform. All listings are created and managed exclusively by third-party sellers.</p>
    <p>Orderer does not own, inspect, certify, endorse, or guarantee any listing, product, service, or property available through the Platform. Any transaction entered into between a buyer and a seller is a direct agreement between those two parties. Orderer is not a party to any such transaction.</p>

    <h2>2. No Warranty on Listings</h2>
    <p>Orderer makes no warranty, express or implied, as to:</p>
    <ul>
        <li>The accuracy, completeness, or truthfulness of any product description, image, specification, or pricing provided by a seller;</li>
        <li>The quality, fitness for purpose, safety, legality, or availability of any item listed on the Platform;</li>
        <li>Whether a seller will fulfil an order, and the time within which it will be fulfilled;</li>
        <li>Whether a product or service meets any particular standard, certification, or regulatory requirement.</li>
    </ul>
    <p>Buyers are responsible for conducting their own due diligence before purchasing, including reading all product descriptions, photographs, and seller reviews carefully.</p>

    <h2>3. Platform Availability</h2>
    <p>Orderer strives to maintain Platform availability at all times but does not warrant uninterrupted, error-free, or secure access. The Platform is provided on an "as is" and "as available" basis. We reserve the right to:</p>
    <ul>
        <li>Suspend or discontinue the Platform or any feature at any time without notice;</li>
        <li>Perform maintenance that may temporarily make the Platform unavailable;</li>
        <li>Modify, replace, or remove any part of the Platform at our sole discretion.</li>
    </ul>
    <p>Orderer is not liable for any loss or damage caused by Platform downtime, errors, bugs, or unavailability.</p>

    <h2>4. Third-Party Links and Services</h2>
    <p>The Platform may contain links to third-party websites, payment processors (Korapay), shipping providers (Shipbubble), cloud services (Cloudinary), email platforms (Brevo), and other external services. These links are provided for convenience only.</p>
    <p>Orderer does not endorse, control, or take responsibility for the content, privacy practices, terms, or availability of any third-party website or service. Your use of third-party services is governed by those parties' own terms and policies.</p>

    <h2>5. Financial and Pricing Information</h2>
    <p>All prices on Orderer are denominated in Nigerian Naira (₦ / NGN) and are set exclusively by individual sellers. Orderer does not set, regulate, or guarantee product prices. Price information may change at any time without notice.</p>
    <p>Orderer is not a financial institution, bank, investment advisor, or payment service provider. Orderer Wallet services are provided solely to facilitate transactions on the Platform and do not constitute a deposit, savings, or investment product. No interest accrues on wallet balances. Funds in wallets are not insured by the Nigerian Deposit Insurance Corporation (NDIC) or any other body.</p>

    <h2>6. Seller Independence</h2>
    <p>Sellers on Orderer are independent third parties and are not employees, agents, partners, joint venturers, or representatives of Orderer. Orderer has no control over and accepts no responsibility for:</p>
    <ul>
        <li>The business practices of any seller;</li>
        <li>The quality of items sold;</li>
        <li>Representations made by sellers to buyers;</li>
        <li>Sellers' compliance with Nigerian tax, consumer protection, NAFDAC, SON, or other regulatory requirements.</li>
    </ul>
    <p>Each seller is solely responsible for compliance with all applicable laws in connection with their listings and fulfilment of orders.</p>

    <h2>7. Limitation of Liability</h2>
    <p>To the fullest extent permitted by the laws of the Federal Republic of Nigeria, Orderer, its directors, officers, employees, agents, and licensors shall not be liable for:</p>
    <ul>
        <li>Any indirect, incidental, special, consequential, or punitive damages;</li>
        <li>Loss of profits, revenue, business, data, or goodwill;</li>
        <li>Damage caused by reliance on any listing, review, or information on the Platform;</li>
        <li>Loss or damage arising from unauthorized access to or alteration of your data;</li>
        <li>Any matter beyond Orderer's reasonable control.</li>
    </ul>
    <p>Orderer's aggregate liability to any user shall not exceed the total fees paid by that user to Orderer in the three (3) calendar months immediately preceding the event giving rise to the claim.</p>

    <h2>8. Consumer Rights Under Nigerian Law</h2>
    <p>Nothing in this Disclaimer limits or excludes any rights you may have as a consumer under the Federal Competition and Consumer Protection Act (FCCPC Act) 2018 or any other applicable Nigerian consumer protection law that cannot be excluded or limited by agreement. If you have a complaint that cannot be resolved through our support channels, you may contact the Federal Competition and Consumer Protection Commission (FCCPC) at <a href="https://fccpc.gov.ng" target="_blank">fccpc.gov.ng</a>.</p>

    <h2>9. Intellectual Property</h2>
    <p>All intellectual property rights in the Orderer Platform, including but not limited to the Orderer name, logo, software, design, and original content created by Orderer, are owned by or licensed to Orderer and are protected under Nigerian and international intellectual property laws. Nothing on the Platform shall be construed as granting any licence or right to use Orderer's intellectual property without prior written permission.</p>

    <h2>10. Indemnification</h2>
    <p>By using the Platform, you agree to indemnify, defend, and hold harmless Orderer and its directors, officers, employees, and agents from and against any claims, damages, losses, liabilities, costs, and expenses (including reasonable legal fees) arising out of or relating to:</p>
    <ul>
        <li>Your use of the Platform;</li>
        <li>Your breach of any of Orderer's terms, policies, or applicable law;</li>
        <li>Any content you submit, post, or transmit through the Platform;</li>
        <li>Your infringement of any third-party right.</li>
    </ul>

    <h2>11. Governing Law</h2>
    <p>This Disclaimer and all matters arising from it are governed by and construed in accordance with the laws of the Federal Republic of Nigeria. Any disputes shall be subject to the jurisdiction of the courts of Lagos State, Nigeria, except where arbitration is required under our <a href="{{ route('legal.terms') }}">Terms and Conditions</a>.</p>

    <h2>12. Changes to This Disclaimer</h2>
    <p>Orderer reserves the right to modify this Disclaimer at any time. Changes will be posted on this page with an updated effective date. Your continued use of the Platform after any changes constitutes your acceptance of the revised Disclaimer.</p>

    <h2>13. Contact</h2>
    <p>For questions about this Disclaimer: <a href="mailto:legal@ordererweb.com">legal@ordererweb.com</a></p>

</div>

@include('layouts.storefront.footer')
<script src="{{ asset('js/jquery/jquery-2.2.4.min.js') }}"></script>
<script src="{{ asset('js/bootstrap.min.js') }}"></script>
<script src="{{ asset('js/classy-nav.min.js') }}"></script>
<script src="{{ asset('js/active.js') }}"></script>
</body>
</html>