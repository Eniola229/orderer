<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Seller Terms — Orderer</title>
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
        .legal-toc{background:#f8f8f8;border-left:3px solid #2ECC71;padding:20px 24px;border-radius:0 8px 8px 0;margin-bottom:40px;}
        .legal-toc p{font-weight:700;margin-bottom:8px;font-size:14px;}
        .legal-toc ol{margin:0;padding-left:18px;}
        .legal-toc li{font-size:13px;margin-bottom:4px;}
        .legal-toc a{color:#2ECC71;text-decoration:none;}
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
        <div class="page-title text-center"><h2>Seller Terms &amp; Conditions</h2></div>
    </div></div></div>
</div>

<div class="legal-wrap">

    <h1>Seller Terms &amp; Conditions</h1>
    <p class="meta">Last updated: {{ date('F d, Y') }} &nbsp;·&nbsp; Applicable to all registered Sellers on Orderer</p>

    <div class="highlight-box">
        <p>By registering as a Seller on Orderer, you agree to these Seller Terms in addition to our general Terms and Conditions and Privacy Policy.</p>
    </div>

    <div class="legal-toc">
        <p>Table of Contents</p>
        <ol>
            <li><a href="#eligibility">Seller Eligibility</a></li>
            <li><a href="#verification">Verification and Approval</a></li>
            <li><a href="#listings">Listing Standards</a></li>
            <li><a href="#pricing">Pricing in Naira</a></li>
            <li><a href="#orders">Order Management</a></li>
            <li><a href="#commission">Commission Structure</a></li>
            <li><a href="#payments">Payments and Withdrawals</a></li>
            <li><a href="#ads">Advertising</a></li>
            <li><a href="#prohibited">Prohibited Listings</a></li>
            <li><a href="#reviews">Reviews and Ratings</a></li>
            <li><a href="#taxes">Tax Obligations</a></li>
            <li><a href="#suspension">Account Suspension and Termination</a></li>
            <li><a href="#liability">Seller Liability</a></li>
            <li><a href="#contact">Contact</a></li>
        </ol>
    </div>

    <h2 id="eligibility">1. Seller Eligibility</h2>
    <p>To register as a Seller on Orderer, you must:</p>
    <ul>
        <li>Be at least 18 years of age;</li>
        <li>Be a resident of Nigeria or have a legal business entity registered in Nigeria;</li>
        <li>Have a valid Nigerian bank account for receiving payouts;</li>
        <li>Provide accurate identification as required during onboarding;</li>
        <li>Agree to these Seller Terms, our general Terms and Conditions, and Privacy Policy.</li>
    </ul>
    <p>Corporate sellers must provide valid CAC registration documents. Individual sellers may be required to provide a government-issued ID (NIN slip, International Passport, or Driver's Licence).</p>

    <h2 id="verification">2. Verification and Approval</h2>
    <p>All seller accounts are subject to review and approval by Orderer's team before listings become publicly visible. The review process may include:</p>
    <ul>
        <li>Identity verification (KYC);</li>
        <li>Review of submitted business documents;</li>
        <li>Background checks as permitted by law.</li>
    </ul>
    <p>Orderer reserves the right to approve or reject any seller application without providing reasons. Approval is not guaranteed. Sellers who provide false or misleading information during onboarding will be permanently banned.</p>

    <div class="warning-box">
        <p>⚠ Verified business sellers receive a "Verified" badge and may access higher withdrawal limits and priority ad placement.</p>
    </div>

    <h2 id="listings">3. Listing Standards</h2>
    <p>All product, service, and property listings must:</p>
    <ul>
        <li>Use accurate, clear, and honest titles and descriptions in English;</li>
        <li>Include at least one high-quality photograph that accurately represents the item;</li>
        <li>State the correct condition (New, Used, Refurbished);</li>
        <li>Display the price in Nigerian Naira (₦) inclusive of any taxes;</li>
        <li>Disclose any material defects, limitations, or relevant terms of sale;</li>
        <li>Comply with all applicable Nigerian consumer protection laws.</li>
    </ul>
    <p>Orderer reviews all listings before they go live. Listings that do not meet these standards will be rejected with feedback. Approved listings may still be taken down if they are found to be inaccurate or in violation of these Terms.</p>

    <h2 id="pricing">4. Pricing in Naira</h2>
    <p>All prices on Orderer must be denominated in Nigerian Naira (₦ / NGN). You are responsible for setting prices that are accurate, fair, and inclusive of applicable taxes. You may update your pricing at any time through your Seller Dashboard. Price changes do not affect orders already placed at the previous price.</p>
    <p>You may not use Orderer to artificially inflate prices, engage in price gouging, or coordinate prices with other sellers.</p>

    <h2 id="orders">5. Order Management</h2>
    <h3>5.1 Processing Time</h3>
    <p>You must state your processing time (the time from order placement to dispatch) on each listing. You are obligated to ship within the stated processing time. Repeated late dispatch will result in warnings and may lead to account suspension.</p>

    <h3>5.2 Shipping</h3>
    <p>You are responsible for selecting a reliable shipping method, providing valid tracking information, and ensuring items are packaged securely to prevent transit damage. Orderer partners with Shipbubble for shipping rate calculation and label generation, but you are not required to use this service exclusively.</p>

    <h3>5.3 Order Cancellation</h3>
    <p>You may cancel an order before it is shipped. Cancellations after the stated processing time has elapsed without shipment will be recorded against your seller performance score. High cancellation rates may result in account restrictions.</p>

    <h3>5.4 Communication</h3>
    <p>You must respond to buyer messages within 48 hours. Failure to communicate in a timely manner is grounds for dispute rulings in the buyer's favour.</p>

    <h2 id="commission">6. Commission Structure</h2>
    <p>Orderer charges a commission on each completed transaction. Commission is calculated as a percentage of the total sale price (excluding delivery fee) and deducted automatically when escrow is released to your wallet.</p>

    <table>
        <thead>
            <tr>
                <th>Category</th>
                <th>Commission Rate</th>
            </tr>
        </thead>
        <tbody>
            <tr><td>Electronics</td><td>8%</td></tr>
            <tr><td>Fashion &amp; Apparel</td><td>10%</td></tr>
            <tr><td>Home &amp; Kitchen</td><td>8%</td></tr>
            <tr><td>Health &amp; Beauty</td><td>10%</td></tr>
            <tr><td>Books &amp; Media</td><td>7%</td></tr>
            <tr><td>Sports &amp; Outdoors</td><td>8%</td></tr>
            <tr><td>Services</td><td>12%</td></tr>
            <tr><td>Real Estate / Properties</td><td>5%</td></tr>
            <tr><td>All Other Categories</td><td>10%</td></tr>
        </tbody>
    </table>

    <p>Commission rates are subject to change with 30 days' advance notice. Orders placed before the effective date of a rate change are governed by the rate at the time of placement.</p>

    <h2 id="payments">7. Payments and Withdrawals</h2>
    <h3>7.1 Earnings</h3>
    <p>Your earnings (sale price minus Orderer's commission) are credited to your Seller Wallet in Nigerian Naira (₦) when escrow is released — either upon buyer confirmation of delivery or 7 days after shipping.</p>

    <h3>7.2 Withdrawal</h3>
    <p>You may request a withdrawal of your available wallet balance at any time, subject to:</p>
    <ul>
        <li>Minimum withdrawal: ₦100;</li>
        <li>You must have a verified bank account on file;</li>
        <li>No more than one pending withdrawal request at a time;</li>
        <li>Orderer processes approved withdrawals within 1–3 business days.</li>
    </ul>
    <p>Orderer reserves the right to hold withdrawals for up to 14 days for new sellers or where fraud is suspected.</p>

    <h3>7.3 Wallet Top-Up</h3>
    <p>You may top up your Seller Wallet or Ads Balance using Korapay or other payment providers avaliable on the site. All top-ups are in Nigerian Naira. Top-up amounts are non-refundable except in cases of platform error.</p>

    <h2 id="ads">8. Advertising</h2>
    <p>Sellers may promote their listings through the Orderer Ads system. Ad campaigns are funded from your Ads Balance (separate from your main Wallet). All ad fees are in Nigerian Naira (₦).</p>
    <ul>
        <li>Ads must be approved by Orderer before going live;</li>
        <li>The budget for an approved ad is deducted upfront from your Ads Balance;</li>
        <li>Orderer does not guarantee any specific level of impressions, clicks, or sales;</li>
        <li>Ad fees are non-refundable once the ad is live;</li>
        <li>Orderer may reject or remove ads at any time for policy violations;</li>
        <li>CPC (Pay Per Order) ads are charged only upon verified completed orders attributed to the ad.</li>
    </ul>

    <h2 id="prohibited">9. Prohibited Listings</h2>
    <p>In addition to the general prohibited items listed in our <a href="{{ route('legal.terms') }}">Terms and Conditions</a>, sellers may not list:</p>
    <ul>
        <li>Items they do not physically possess or have the right to sell;</li>
        <li>Pre-order items without a confirmed restocking date;</li>
        <li>Dropshipping listings where delivery times are misrepresented;</li>
        <li>Cloned or counterfeit electronics or pharmaceuticals;</li>
        <li>NAFDAC-regulated food or drug products without valid registration numbers;</li>
        <li>Second-hand underwear, swimwear, or intimate apparel;</li>
        <li>Any item whose sale would violate NAFDAC, SON, or other regulatory body guidelines.</li>
    </ul>

    <h2 id="reviews">10. Reviews and Ratings</h2>
    <p>Buyers may leave ratings and reviews for completed orders. You may not:</p>
    <ul>
        <li>Offer incentives (money, gifts, discounts) in exchange for positive reviews;</li>
        <li>Create fake buyer accounts to review your own products;</li>
        <li>Threaten or harass buyers who leave negative reviews.</li>
    </ul>
    <p>Violation of this policy will result in immediate account suspension.</p>

    <h2 id="taxes">11. Tax Obligations</h2>
    <p>You are solely responsible for determining and paying all applicable Nigerian taxes on your sales income, including but not limited to:</p>
    <ul>
        <li>Value Added Tax (VAT) at the applicable rate under the Value Added Tax Act;</li>
        <li>Personal Income Tax (PIT) or Company Income Tax (CIT) as applicable;</li>
        <li>Withholding Tax where applicable.</li>
    </ul>
    <p>Orderer may be required by the Federal Inland Revenue Service (FIRS) or other authorities to report seller transaction data. By using the Platform, you consent to such reporting.</p>

    <h2 id="suspension">12. Account Suspension and Termination</h2>
    <p>Orderer may suspend or permanently terminate a seller account for:</p>
    <ul>
        <li>Repeated or severe policy violations;</li>
        <li>High dispute or cancellation rates;</li>
        <li>Providing false information during registration;</li>
        <li>Fraudulent activity;</li>
        <li>Persistent failure to fulfil orders;</li>
        <li>Any activity that threatens the integrity of the Platform.</li>
    </ul>
    <p>Upon termination, pending payouts will be processed after deducting any amounts owed, subject to a hold period of up to 90 days to cover potential chargebacks and disputes.</p>

    <h2 id="liability">13. Seller Liability</h2>
    <p>You are fully liable for all claims arising from your listings and fulfilment of orders, including but not limited to product liability, intellectual property infringement, and consumer protection claims. Orderer acts solely as a marketplace facilitator and accepts no liability for seller actions.</p>
    <p>You agree to indemnify and hold Orderer harmless from any claims, damages, losses, or expenses (including legal fees) arising from your violation of these Terms or applicable Nigerian law.</p>

    <h2 id="contact">14. Contact</h2>
    <p>For seller enquiries: <a href="mailto:sellers@ordererweb.com">sellers@ordererweb.com</a></p>
    <p>For dispute escalation: open a support ticket from your Seller Dashboard.</p>

</div>

@include('layouts.storefront.footer')
<script src="{{ asset('js/jquery/jquery-2.2.4.min.js') }}"></script>
<script src="{{ asset('js/bootstrap.min.js') }}"></script>
<script src="{{ asset('js/classy-nav.min.js') }}"></script>
<script src="{{ asset('js/active.js') }}"></script>
</body>
</html>