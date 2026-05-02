<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Shipping Policy — Orderer</title>
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
        table{width:100%;border-collapse:collapse;margin:16px 0;}
        th,td{padding:10px 14px;text-align:left;border:1px solid #eee;font-size:14px;}
        th{background:#f8f8f8;font-weight:700;color:#333;}
    </style>
</head>
<body>

@auth('web')@include('layouts.storefront.header-auth')@else @include('layouts.storefront.header-guest')@endauth
@include('layouts.storefront.cart-sidebar')
@include('layouts.partials.alerts')

<div class="breadcumb_area bg-img" style="background-image:url({{ asset('img/bg-img/breadcumb.jpeg') }});">
    <div class="container h-100"><div class="row h-100 align-items-center"><div class="col-12">
        <div class="page-title text-center"><h2>Shipping Policy</h2></div>
    </div></div></div>
</div>

<div class="legal-wrap">

    <h1>Shipping Policy</h1>
    <p class="meta">Last updated: {{ date('F d, Y') }} &nbsp;·&nbsp; All shipping fees in Nigerian Naira (₦)</p>

    <div class="highlight-box">
        <p>🚚 Orderer partners with Shipbubble to offer local and international shipping. Shipping rates are calculated at checkout based on your delivery address, package weight, and selected carrier.</p>
    </div>

    <h2>1. How Shipping Works on Orderer</h2>
    <p>Orderer is a marketplace where each Seller is responsible for shipping their own products. When you place an order:</p>
    <ol>
        <li>The Seller receives your order and prepares the shipment;</li>
        <li>The Seller books a delivery carrier via Orderer's integrated Shipbubble platform or their own arrangement;</li>
        <li>A tracking number is provided to you once the order is dispatched;</li>
        <li>You track your delivery in real time from your order page.</li>
    </ol>

    <h2>2. Shipping Rates</h2>
    <p>Shipping fees are calculated at checkout and depend on:</p>
    <ul>
        <li>Your delivery location (state and country);</li>
        <li>The weight and dimensions of the package;</li>
        <li>Your selected shipping carrier and service level.</li>
    </ul>
    <p>Shipping fees are displayed in Nigerian Naira (₦) before you confirm your order. Fees are non-refundable unless the order is cancelled before dispatch or lost in transit.</p>

    <h2>3. Estimated Delivery Timeframes</h2>
    <table>
        <thead>
            <tr>
                <th>Destination</th>
                <th>Service</th>
                <th>Estimated Timeframe</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Same city (within Lagos, Abuja, etc.)</td>
                <td>Express Rider</td>
                <td>Same day – 24 hours</td>
            </tr>
            <tr>
                <td>Within Nigeria (interstate)</td>
                <td>Standard</td>
                <td>2 – 5 business days</td>
            </tr>
            <tr>
                <td>Within Nigeria (interstate)</td>
                <td>Express</td>
                <td>1 – 2 business days</td>
            </tr>
            <tr>
                <td>West Africa (Ghana, Côte d'Ivoire, etc.)</td>
                <td>Standard International</td>
                <td>5 – 10 business days</td>
            </tr>
            <tr>
                <td>UK / Europe</td>
                <td>DHL / FedEx Standard</td>
                <td>7 – 14 business days</td>
            </tr>
            <tr>
                <td>USA / Canada</td>
                <td>DHL / FedEx Standard</td>
                <td>7 – 14 business days</td>
            </tr>
            <tr>
                <td>International (all regions)</td>
                <td>Express</td>
                <td>3 – 5 business days</td>
            </tr>
        </tbody>
    </table>

    <div class="warning-box">
        <p>⚠ Delivery timeframes are estimates and may vary due to carrier delays, customs processing, public holidays, or force majeure events. Orderer does not guarantee delivery within any specific timeframe.</p>
    </div>

    <h2>4. Tracking Your Order</h2>
    <p>Once your order is shipped, you will receive:</p>
    <ul>
        <li>A notification in your Orderer account;</li>
        <li>An email with your tracking number and carrier details;</li>
        <li>A live tracking link accessible from your Order Details page.</li>
    </ul>
    <p>You can track your shipment at any time by visiting <strong>My Orders → View Order → Track Shipment</strong> in your Buyer dashboard.</p>

    <h2>5. Book a Rider (Delivery Booking)</h2>
    <p>In addition to product orders, Orderer offers a standalone delivery booking service. You can book a rider for local or international package delivery even without purchasing a product on the Platform.</p>
    <ul>
        <li>Enter pickup and delivery details;</li>
        <li>View available carriers and rates;</li>
        <li>Pay from your Orderer Wallet or via Korapay;</li>
        <li>Track your delivery in real time.</li>
    </ul>
    <p><a href="{{ route('rider.booking') }}">Book a delivery →</a></p>

    <h2>6. International Shipments and Customs</h2>
    <p>For international orders, the recipient (buyer) is responsible for:</p>
    <ul>
        <li>All import duties, taxes, and customs clearance fees charged by the destination country;</li>
        <li>Providing accurate import documentation if required;</li>
        <li>Compliance with the destination country's import regulations.</li>
    </ul>
    <p>Orderer and Sellers are not liable for packages held or seized by customs authorities.</p>

    <h2>7. Lost, Damaged, or Delayed Packages</h2>
    <h3>Lost Packages</h3>
    <p>If your tracking shows no movement for 7 or more consecutive business days, raise a dispute from your Order Details page. We will investigate with the carrier and process a resolution within 10 business days.</p>

    <h3>Damaged in Transit</h3>
    <p>If your item arrives damaged, do not confirm delivery. Raise a dispute immediately with photographs as evidence. Do not discard the packaging — it may be required for the carrier's insurance claim.</p>

    <h3>Delayed Packages</h3>
    <p>Minor delays are common, especially during peak seasons. If your order significantly exceeds the estimated delivery window, contact our support team and we will liaise with the carrier on your behalf.</p>

    <h2>8. Seller Shipping Obligations</h2>
    <p>Sellers must:</p>
    <ul>
        <li>Ship within their stated processing time (maximum 5 business days unless agreed otherwise);</li>
        <li>Use appropriate, secure packaging;</li>
        <li>Provide a valid tracking number before marking an order as shipped;</li>
        <li>Declare accurate item values and descriptions on customs forms for international shipments.</li>
    </ul>

    <h2>9. Contact</h2>
    <p>For shipping queries: <a href="{{ route('contact') }}">ordererweb.com/contact</a> or open a ticket from your dashboard.</p>

</div>

@include('layouts.storefront.footer')
<script src="{{ asset('js/jquery/jquery-2.2.4.min.js') }}"></script>
<script src="{{ asset('js/bootstrap.min.js') }}"></script>
<script src="{{ asset('js/classy-nav.min.js') }}"></script>
<script src="{{ asset('js/active.js') }}"></script>
</body>
</html>