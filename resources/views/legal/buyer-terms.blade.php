<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Buyer Terms — Orderer</title>
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
    </style>
</head>
<body>

@auth('web')@include('layouts.storefront.header-auth')
@else@include('layouts.storefront.header-guest')@endauth

<div class="breadcumb_area bg-img" style="background-image:url({{ asset('img/bg-img/breadcumb.jpeg') }});">
    <div class="container h-100"><div class="row h-100 align-items-center"><div class="col-12">
        <div class="page-title text-center"><h2>Buyer Terms</h2></div>
    </div></div></div>
</div>

<div class="legal-wrap">

    <h1>Buyer Terms &amp; Conditions</h1>
    <p class="meta">Last updated: {{ date('F d, Y') }} &nbsp;·&nbsp; Applicable to all Buyers on Orderer</p>

    <div class="highlight-box">
        <p>🛒 These Buyer Terms apply in addition to our general Terms and Conditions. By placing an order on Orderer, you confirm that you have read and agree to these terms.</p>
    </div>

    <h2>1. Making a Purchase</h2>
    <p>By clicking "Place Order", you are entering into a binding purchase agreement with the Seller (not Orderer). Orderer facilitates the transaction by providing the Platform, processing payment, and holding funds in escrow until you confirm delivery.</p>
    <p>Before placing an order, you are responsible for:</p>
    <ul>
        <li>Reading the full product description and all photographs carefully;</li>
        <li>Verifying that the item meets your requirements before purchasing;</li>
        <li>Ensuring your delivery address is accurate and complete;</li>
        <li>Confirming that you have sufficient funds in your Wallet or valid payment method.</li>
    </ul>

    <h2>2. Payment</h2>
    <p>All payments on Orderer are in Nigerian Naira (₦). You may pay using:</p>
    <ul>
        <li><strong>Orderer Wallet:</strong> your pre-loaded NGN balance;</li>
        <li><strong>Korapay:</strong> debit/credit card or bank transfer.</li>
    </ul>
    <p>Payment is processed securely. Orderer does not store your card details. By paying, you confirm that you are the authorised user of the payment method.</p>

    <h2>3. Escrow Protection</h2>
    <p>Your payment is held in escrow and is not released to the Seller until:</p>
    <ul>
        <li>You confirm that you have received your order in satisfactory condition; or</li>
        <li>7 days have elapsed after the order is marked as shipped without you raising a dispute.</li>
    </ul>
    <p>Do not confirm delivery until you have inspected your item. Once you confirm delivery, the transaction is considered complete and a refund may no longer be possible.</p>

    <h2>4. Delivery and Tracking</h2>
    <p>Estimated delivery times are provided by Sellers and are indicative only. Orderer does not guarantee delivery within any specific timeframe. You are responsible for monitoring tracking updates and being available to receive your delivery.</p>
    <p>If your order is marked as delivered but you have not received it, you must raise a dispute within 48 hours of the delivery notification.</p>

    <h2>5. Cancellations</h2>
    <p>You may request a cancellation before the Seller ships your order. Once an item has been shipped, cancellation is not possible — you must wait to receive the item and then raise a dispute if there is an issue.</p>

    <h2>6. Returns and Refunds</h2>
    <p>Please review our <a href="{{ route('legal.refund') }}">Refund Policy</a> for full details. In summary:</p>
    <ul>
        <li>Raise a dispute before confirming delivery if the item is wrong, damaged, or not as described;</li>
        <li>Approved refunds are credited to your Orderer Wallet within 24 hours of the decision;</li>
        <li>Buyer's remorse (changing your mind) is not a valid reason for a refund.</li>
    </ul>

    <h2>7. Reviews</h2>
    <p>After receiving your order, you are encouraged to leave an honest review. Reviews must be:</p>
    <ul>
        <li>Based on your genuine experience with the item and seller;</li>
        <li>Factual and not defamatory;</li>
        <li>Free from offensive or inappropriate language.</li>
    </ul>
    <p>Orderer reserves the right to remove reviews that violate these guidelines.</p>

    <h2>8. Wallet</h2>
    <p>Your Orderer Wallet holds Nigerian Naira (₦) that you have loaded or received as refunds or referral bonuses. Wallet balances:</p>
    <ul>
        <li>Are non-transferable to other users;</li>
        <li>Do not expire while your account is active;</li>
        <li>Cannot be withdrawn to a bank account (Buyer wallets are for purchases only);</li>
        <li>Are forfeited upon account termination for policy violations.</li>
    </ul>

    <h2>9. Referral Programme</h2>
    <p>When you refer a friend using your unique referral link and they complete their first order:</p>
    <ul>
        <li>You receive a referral bonus credited to your Wallet;</li>
        <li>Your friend receives a welcome bonus on their first order.</li>
    </ul>
    <p>Referral bonuses are subject to change. Abuse of the referral system (creating fake accounts, etc.) will result in account termination and forfeiture of all bonuses.</p>

    <h2>10. Prohibited Buyer Conduct</h2>
    <ul>
        <li>Misusing the dispute process to obtain free goods;</li>
        <li>Confirming delivery before receiving the item to gain an advantage in a dispute;</li>
        <li>Purchasing for resale without complying with applicable Nigerian regulations;</li>
        <li>Using fraudulent payment methods;</li>
        <li>Harassing sellers or Orderer support staff.</li>
    </ul>

    <h2>11. Contact</h2>
    <p>For buyer support: <a href="{{ route('contact') }}">ordererweb.shop/contact</a> or open a support ticket from your dashboard.</p>

</div>

@include('layouts.storefront.footer')
<script src="{{ asset('js/jquery/jquery-2.2.4.min.js') }}"></script>
<script src="{{ asset('js/bootstrap.min.js') }}"></script>
<script src="{{ asset('js/classy-nav.min.js') }}"></script>
<script src="{{ asset('js/active.js') }}"></script>
</body>
</html>