<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Refund Policy — Orderer</title>
    <link rel="icon" href="{{ asset('img/core-img/favicon.png') }}">
    <link rel="stylesheet" href="{{ asset('css/core-style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/orderer.css') }}">
    <style>
        .legal-wrap{max-width:860px;margin:0 auto;padding:60px 24px 80px;}
        .legal-wrap h1{font-size:32px;font-weight:800;color:#1a1a1a;margin-bottom:6px;}
        .legal-wrap .meta{color:#888;font-size:14px;margin-bottom:40px;}
        .legal-wrap h2{font-size:20px;font-weight:700;color:#1a1a1a;margin:36px 0 12px;}
        .legal-wrap h3{font-size:16px;font-weight:700;color:#333;margin:24px 0 8px;}
        .legal-wrap p,.legal-wrap li{font-size:15px;color:#444;line-height:1.85;}
        .legal-wrap ul,.legal-wrap ol{padding-left:22px;margin-bottom:16px;}
        .legal-wrap li{margin-bottom:6px;}
        .legal-wrap a{color:#2ECC71;}
        .highlight-box{background:#D5F5E3;border-radius:8px;padding:16px 20px;margin:20px 0;}
        .highlight-box p{margin:0;font-size:14px;color:#1E8449;font-weight:600;}
        .warning-box{background:#FEF9E7;border-radius:8px;padding:16px 20px;margin:20px 0;border-left:3px solid #F9CA24;}
        .warning-box p{margin:0;font-size:14px;color:#B7950B;font-weight:600;}
        .denied-box{background:#FADBD8;border-radius:8px;padding:16px 20px;margin:20px 0;border-left:3px solid #E74C3C;}
        .denied-box p{margin:0;font-size:14px;color:#A93226;font-weight:600;}
        .timeline{border-left:3px solid #2ECC71;padding-left:20px;margin:16px 0;}
        .timeline-item{position:relative;margin-bottom:20px;}
        .timeline-item::before{content:'';position:absolute;left:-26px;top:5px;width:10px;height:10px;border-radius:50%;background:#2ECC71;}
        .timeline-item p{margin:0;font-size:15px;color:#444;}
        .timeline-item .day{font-weight:700;color:#1a1a1a;margin-bottom:3px;}
    </style>
</head>
<body>

@auth('web')@include('layouts.storefront.header-auth')@else @include('layouts.storefront.header-guest')@endauth
@include('layouts.storefront.cart-sidebar')
@include('layouts.partials.alerts')

<div class="breadcumb_area bg-img" style="background-image:url({{ asset('img/bg-img/breadcumb.jpeg') }});">
    <div class="container h-100"><div class="row h-100 align-items-center"><div class="col-12">
        <div class="page-title text-center"><h2>Refund Policy</h2></div>
    </div></div></div>
</div>

<div class="legal-wrap">

    <h1>Refund &amp; Returns Policy</h1>
    <p class="meta">Last updated: {{ date('F d, Y') }} &nbsp;·&nbsp; All refunds issued in Nigerian Naira (₦)</p>

    <div class="highlight-box">
        <p>✅ Orderer uses an escrow system. Your money is protected — funds are only released to the seller after you confirm delivery or 7 days have passed.</p>
    </div>

    <h2>1. How Our Escrow Protects You</h2>
    <p>When you place an order on Orderer, your payment is held securely in escrow. The seller does not receive funds until you confirm that you have received your order in satisfactory condition.</p>
    <p>If you have an issue with your order, you must raise a dispute <strong>before</strong> confirming delivery, or within the 7-day window after the order is marked as shipped. Once you confirm delivery or 7 days elapse without a dispute, funds are automatically released and refunds may no longer be possible.</p>

    <h2>2. Refund Timeline</h2>
    <div class="timeline">
        <div class="timeline-item">
            <p class="day">Day 0 — Order Placed</p>
            <p>Payment held in escrow. Seller begins processing your order.</p>
        </div>
        <div class="timeline-item">
            <p class="day">Day 1–7 after shipment — Delivery Window</p>
            <p>You may raise a dispute at any point during this window if there is a problem with your order.</p>
        </div>
        <div class="timeline-item">
            <p class="day">Day 7 after shipment — Auto-release</p>
            <p>If no dispute is raised, funds are automatically released to the seller. Refund requests after this point are at Orderer's sole discretion.</p>
        </div>
        <div class="timeline-item">
            <p class="day">You confirm delivery — Immediate release</p>
            <p>Funds are released to the seller immediately upon your confirmation. Raise any dispute before confirming.</p>
        </div>
    </div>

    <h2>3. Eligible Refund Scenarios</h2>
    <p>You are entitled to a full refund to your Orderer Wallet under the following circumstances:</p>
    <ul>
        <li><strong>Item not delivered:</strong> the order was shipped but never arrived and the seller cannot provide proof of delivery;</li>
        <li><strong>Item significantly not as described:</strong> the item received is materially different from the listing description (wrong item, wrong colour, wrong size, etc.);</li>
        <li><strong>Damaged in transit:</strong> the item arrived visibly damaged due to shipping;</li>
        <li><strong>Defective/non-functional:</strong> the item does not work as described and the seller refuses to remedy it;</li>
        <li><strong>Order cancelled before shipment:</strong> the seller fails to ship within the stated processing time.</li>
    </ul>

    <h2>4. Non-Refundable Situations</h2>
    <div class="denied-box">
        <p>❌ The following situations do not qualify for a refund:</p>
    </div>
    <ul>
        <li>You changed your mind after receiving the item (buyer's remorse);</li>
        <li>You confirmed delivery before inspecting the item;</li>
        <li>The item description and images were accurate and you did not read them carefully;</li>
        <li>Damage caused by you after receipt;</li>
        <li>Digital goods (downloads, activation codes, access keys) once delivered;</li>
        <li>Perishable goods (food, flowers, etc.) unless undelivered or spoiled in transit;</li>
        <li>Services already rendered by the seller;</li>
        <li>Items from categories explicitly marked as "Final Sale" or "Non-Refundable" at the time of purchase;</li>
        <li>Disputes raised more than 7 days after the order was marked as shipped.</li>
    </ul>

    <h2>5. How to Raise a Dispute</h2>
    <ol>
        <li>Log into your Orderer account;</li>
        <li>Go to <strong>My Orders</strong> and select the relevant order;</li>
        <li>Click <strong>"Raise a Dispute"</strong> before confirming delivery;</li>
        <li>Provide a clear description of the issue and upload supporting evidence (photos, videos, screenshots);</li>
        <li>Our support team will review your case within <strong>3–5 business days</strong>.</li>
    </ol>
    <p>Alternatively, contact our support team at <a href="{{ route('contact') }}">ordererweb.shop/contact</a> or open a support ticket from your account dashboard.</p>

    <h2>6. Dispute Resolution Process</h2>
    <p>Once a dispute is raised:</p>
    <ul>
        <li>Funds remain frozen in escrow;</li>
        <li>Both the Buyer and Seller are notified;</li>
        <li>Both parties have <strong>48 hours</strong> to submit their evidence;</li>
        <li>Orderer's support team reviews all evidence and issues a decision within <strong>5 business days</strong>;</li>
        <li>Orderer's decision is final and binding.</li>
    </ul>

    <h2>7. Refund Processing</h2>
    <p>If a refund is approved:</p>
    <ul>
        <li>The refund amount is credited to your <strong>Orderer Wallet</strong> in Nigerian Naira (₦) within 24 hours of the decision;</li>
        <li>Wallet funds may be used immediately for future purchases;</li>
        <li>If you prefer a refund to your original payment method, please request this explicitly in your dispute ticket. Bank transfer refunds may take 3–7 business days and are subject to Korapay's processing timelines.</li>
    </ul>
    <p>Orderer does not charge any fee for processing refunds.</p>

    <h2>8. Seller Responsibilities</h2>
    <p>Sellers are responsible for:</p>
    <ul>
        <li>Accurate listing descriptions and images;</li>
        <li>Packaging items securely to prevent transit damage;</li>
        <li>Shipping within their stated processing time;</li>
        <li>Providing valid tracking information.</li>
    </ul>
    <p>Sellers found to have systematically misrepresented listings or violated this policy may have their accounts suspended and outstanding balances withheld pending investigation.</p>

    <h2>9. Advertising Fees</h2>
    <p>Ad campaign fees paid from a Seller's Ads Balance are non-refundable once an ad has been approved and activated, as the service (ad impressions and exposure) has been delivered. Unspent budget at the time of ad deletion or rejection is refunded to the Ads Balance, not the main wallet.</p>

    <h2>10. Contact</h2>
    <p>For refund queries: <a href="mailto:support@ordererweb.shop">support@ordererweb.shop</a> or open a ticket from your dashboard.</p>

</div>

@include('layouts.storefront.footer')
<script src="{{ asset('js/jquery/jquery-2.2.4.min.js') }}"></script>
<script src="{{ asset('js/bootstrap.min.js') }}"></script>
<script src="{{ asset('js/classy-nav.min.js') }}"></script>
<script src="{{ asset('js/active.js') }}"></script>
</body>
</html>