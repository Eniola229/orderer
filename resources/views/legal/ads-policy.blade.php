<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Ads Policy — Orderer</title>
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
        <div class="page-title text-center"><h2>Ads Policy</h2></div>
    </div></div></div>
</div>

<div class="legal-wrap">

    <h1>Ads Policy</h1>
    <p class="meta">Last updated: {{ date('F d, Y') }} &nbsp;·&nbsp; Applicable to all Sellers running promotions on Orderer</p>

    <div class="highlight-box">
        <p>By submitting an ad on Orderer, you agree to this Ads Policy in addition to our Seller Terms &amp; Conditions and general Terms and Conditions.</p>
    </div>

    <div class="legal-toc">
        <p>Table of Contents</p>
        <ol>
            <li><a href="#overview">Overview</a></li>
            <li><a href="#eligibility">Advertiser Eligibility</a></li>
            <li><a href="#ad-types">Ad Types and Placements</a></li>
            <li><a href="#approval">Ad Review and Approval</a></li>
            <li><a href="#content-standards">Ad Content Standards</a></li>
            <li><a href="#prohibited-content">Prohibited Ad Content</a></li>
            <li><a href="#billing">Billing and Charges</a></li>
            <li><a href="#refunds">Refunds and Credits</a></li>
            <li><a href="#performance">Performance and Reporting</a></li>
            <li><a href="#suspension">Ad Suspension and Removal</a></li>
            <li><a href="#liability">Advertiser Liability</a></li>
            <li><a href="#changes">Changes to This Policy</a></li>
            <li><a href="#contact">Contact</a></li>
        </ol>
    </div>

    <h2 id="overview">1. Overview</h2>
    <p>Orderer's advertising system ("Orderer Ads") allows approved sellers to promote their products, services, properties, and brands to buyers on the Platform. Ads are funded from your Ads Balance — a dedicated wallet separate from your main Seller Wallet — and are subject to approval before going live.</p>
    <p>All advertising on Orderer must be truthful, not misleading, and compliant with applicable Nigerian law, including the Nigerian Code of Advertising Practice and the Federal Competition and Consumer Protection Act (FCCPA).</p>

    <h2 id="eligibility">2. Advertiser Eligibility</h2>
    <p>To create and run ads on Orderer, you must:</p>
    <ul>
        <li>Have an active, verified Seller account in good standing;</li>
        <li>Have sufficient Ads Balance to cover the full campaign budget at the time of submission;</li>
        <li>Have at least one approved listing (product, service, or property) to promote, or be promoting your brand/store;</li>
        <li>Agree to this Ads Policy and all other applicable Orderer policies.</li>
    </ul>
    <p>Sellers who are suspended, under investigation, or subject to payout holds are not eligible to run ads until their account is fully restored.</p>

    <h2 id="ad-types">3. Ad Types and Placements</h2>
    <p>Orderer currently offers the following ad formats:</p>

    <h3>3.1 Banner Image Ad</h3>
    <p>A static image displayed in banner slideshow slots across the Platform. Charged on a cost-per-day basis. Available in multiple placement slots (Homepage Hero, Category Page, Product Sidebar, Search Results).</p>

    <h3>3.2 Banner Video Ad (Premium)</h3>
    <p>A short video displayed in banner slideshow slots. Charged on a cost-per-day basis at a premium rate reflecting the higher visibility and engagement of video. Video ads require additional review time.</p>

    <h3>3.3 Top Listing</h3>
    <p>Your product, service, or property is pinned at the top of relevant category and search result pages. Charged on a cost-per-day basis. The listing must be the one being promoted — you may not use a Top Listing ad to redirect buyers to a different item.</p>

    <h3>3.4 Pay Per Order (CPC)</h3>
    <p>You are charged a fixed fee only when a buyer places a verified, completed order that is directly attributed to the ad. There is no charge for impressions or clicks alone. CPC rates are set per ad category and are displayed before you submit your campaign.</p>

    <table>
        <thead>
            <tr>
                <th>Ad Type</th>
                <th>Billing Model</th>
                <th>Slot Required</th>
                <th>Media Required</th>
            </tr>
        </thead>
        <tbody>
            <tr><td>Banner Image</td><td>Cost per day</td><td>Yes</td><td>Image (JPG/PNG)</td></tr>
            <tr><td>Banner Video</td><td>Cost per day (premium)</td><td>Yes</td><td>Video (MP4/MOV)</td></tr>
            <tr><td>Top Listing</td><td>Cost per day</td><td>No</td><td>Optional</td></tr>
            <tr><td>Pay Per Order (CPC)</td><td>Per completed order</td><td>No</td><td>Optional</td></tr>
        </tbody>
    </table>

    <h2 id="approval">4. Ad Review and Approval</h2>
    <p>All ads are manually reviewed by Orderer's team before going live. The review process typically takes up to 24 hours on business days (Monday–Friday, 9am–5pm WAT), and up to 48 hours on weekends or public holidays.</p>
    <p>During review, Orderer checks that:</p>
    <ul>
        <li>The ad content complies with this Ads Policy and applicable law;</li>
        <li>The promoted listing is approved and accurately represented in the ad;</li>
        <li>The media meets the required format, size, and quality standards;</li>
        <li>The Ads Balance contains sufficient funds to cover the full campaign budget.</li>
    </ul>
    <p>If your ad is rejected, you will be notified with a reason. You may edit and resubmit the ad after addressing the stated issues. Orderer reserves the right to reject any ad without further obligation.</p>

    <div class="highlight-box">
        <p>✓ No funds are held upfront. Your Ads Balance is only charged daily while your ad is actively running.</p>
    </div>

    <h2 id="content-standards">5. Ad Content Standards</h2>
    <p>All ad creatives (images, videos, and titles) must:</p>
    <ul>
        <li>Accurately represent the product, service, property, or brand being promoted;</li>
        <li>Be in English or include an English translation;</li>
        <li>Have a clear, legible title that is not misleading;</li>
        <li>For image ads: be high resolution and free of excessive text overlay (text should not cover more than 20% of the image area);</li>
        <li>For video ads: be at least 5 seconds and no longer than 60 seconds; include no audio that autoplays at high volume;</li>
        <li>Not impersonate Orderer, any of its staff, or any other seller or brand;</li>
        <li>Not use fabricated or unverified claims (e.g. "Nigeria's No. 1") unless you can substantiate them;</li>
        <li>Not display prices that differ from the listed price of the promoted item.</li>
    </ul>

    <h3>5.1 Image Specifications</h3>
    <table>
        <thead>
            <tr><th>Specification</th><th>Requirement</th></tr>
        </thead>
        <tbody>
            <tr><td>Accepted formats</td><td>JPG, PNG</td></tr>
            <tr><td>Maximum file size</td><td>10 MB</td></tr>
            <tr><td>Minimum resolution</td><td>800 × 400 px</td></tr>
            <tr><td>Recommended aspect ratio</td><td>2:1 (landscape)</td></tr>
        </tbody>
    </table>

    <h3>5.2 Video Specifications</h3>
    <table>
        <thead>
            <tr><th>Specification</th><th>Requirement</th></tr>
        </thead>
        <tbody>
            <tr><td>Accepted formats</td><td>MP4, MOV</td></tr>
            <tr><td>Maximum file size</td><td>50 MB</td></tr>
            <tr><td>Duration</td><td>5–60 seconds</td></tr>
            <tr><td>Minimum resolution</td><td>720p (1280 × 720)</td></tr>
        </tbody>
    </table>

    <h2 id="prohibited-content">6. Prohibited Ad Content</h2>
    <p>In addition to items prohibited under our <a href="{{ route('legal.seller-terms') }}">Seller Terms</a> and <a href="{{ route('legal.terms') }}">Terms and Conditions</a>, ads must not promote or contain:</p>
    <ul>
        <li>Products or services that are illegal under Nigerian federal or state law;</li>
        <li>Counterfeit, clone, or replica goods;</li>
        <li>NAFDAC-regulated drugs, food products, or cosmetics without displaying a valid NAFDAC registration number;</li>
        <li>Alcohol or tobacco products targeted at persons under 18;</li>
        <li>Gambling, betting, or lottery services not licensed by the National Lottery Regulatory Commission (NLRC);</li>
        <li>Financial or investment products not registered with the Securities and Exchange Commission (SEC) of Nigeria;</li>
        <li>Get-rich-quick schemes, pyramid schemes, or multi-level marketing (MLM) recruitment;</li>
        <li>Political campaign materials or content endorsing or opposing any political party or candidate;</li>
        <li>Religious content intended to solicit donations or recruit members;</li>
        <li>Sexually explicit or suggestive imagery;</li>
        <li>Content that discriminates against any person on the basis of race, ethnicity, religion, gender, age, disability, or sexual orientation;</li>
        <li>Misleading before-and-after imagery for health or weight-loss products;</li>
        <li>Ads that simulate system alerts, error messages, or Orderer notifications to deceive buyers;</li>
        <li>Ads that auto-redirect buyers away from the Orderer Platform.</li>
    </ul>

    <div class="warning-box">
        <p>⚠ Submitting an ad for a prohibited item is a violation of Seller Terms and may result in immediate account suspension, regardless of whether the ad was approved.</p>
    </div>

    <h2 id="billing">7. Billing and Charges</h2>

    <h3>7.1 Ads Balance</h3>
    <p>All ad spend is deducted from your Ads Balance, which is separate from your main Seller Wallet. You may top up your Ads Balance at any time via Korapay or other payment providers available on the Platform. All amounts are in Nigerian Naira (₦). No funds are held or reserved upfront — you are only charged for days your ad actually runs.</p>

    <h3>7.2 Daily Deduction</h3>
    <p>For daily-rate ads (Banner Image, Banner Video, and Top Listing), the applicable daily rate for your chosen slot is deducted from your Ads Balance each day the ad is active. For CPC (Pay Per Order) ads, the per-order fee is deducted from your Ads Balance each time a verified completed order is attributed to the ad.</p>

    <h3>7.3 Insufficient Balance — Automatic Pause</h3>
    <p>Your ad will be automatically paused on any day where your Ads Balance does not have sufficient funds to cover that day's charge. The ad resumes automatically on the next day that your balance is sufficient, provided the campaign end date has not passed. Paused days are not charged. Orderer is not liable for any loss of impressions, visibility, or sales resulting from an automatically paused campaign due to low balance.</p>

    <div class="warning-box">
        <p>⚠ Keep your Ads Balance topped up to avoid unexpected pauses. You will receive a notification when your balance runs low.</p>
    </div>

    <h3>7.4 Early Termination</h3>
    <p>You may cancel a live campaign at any time from your Seller Dashboard. Cancellation takes effect immediately and no further daily charges will be applied. Amounts already deducted for days the ad was live are non-refundable.</p>

    <h2 id="refunds">8. Refunds and Credits</h2>
    <p>Ad fees are generally non-refundable. Exceptions are limited to:</p>
    <ul>
        <li><strong>Platform error:</strong> If a confirmed platform error caused your ad to not be served despite being live, Orderer will credit the affected days back to your Ads Balance upon verified request;</li>
        <li><strong>Orderer-initiated removal:</strong> If Orderer removes your ad for reasons other than a policy violation (e.g. a change in platform structure), no further daily charges will be applied from the date of removal;</li>
        <li><strong>Duplicate charges:</strong> Confirmed duplicate deductions will be fully reversed.</li>
    </ul>
    <p>To request a credit, contact <a href="mailto:sellers@ordererweb.com">sellers@ordererweb.com</a> within 7 days of the issue with your ad ID and a description of the problem.</p>

    <h2 id="performance">9. Performance and Reporting</h2>
    <p>Orderer provides basic campaign performance metrics in your Seller Dashboard, including impressions, clicks (where applicable), and attributed orders (for CPC ads). These figures are for informational purposes only.</p>
    <p>Orderer does not guarantee any specific level of impressions, clicks, orders, or revenue from any ad campaign. Ad performance is influenced by factors outside Orderer's control, including seasonal demand, competition, and buyer behaviour.</p>
    <p>You may not use performance data from Orderer Ads to make claims in external advertising without Orderer's prior written consent.</p>

    <h2 id="suspension">10. Ad Suspension and Removal</h2>
    <p>Orderer may pause, remove, or permanently reject an ad at any time if it:</p>
    <ul>
        <li>Violates this Ads Policy or any other Orderer policy;</li>
        <li>Relates to a listing that has been removed or suspended;</li>
        <li>Is associated with a seller account that has been suspended or restricted;</li>
        <li>Is the subject of a buyer complaint or regulatory inquiry;</li>
        <li>Is found to contain inaccurate or misleading information after going live.</li>
    </ul>
    <p>Where an ad is removed due to a policy violation, the spent portion of the budget is non-refundable. Repeated ad policy violations will result in your advertising privileges being restricted or permanently revoked, and may lead to seller account suspension.</p>

    <h2 id="liability">11. Advertiser Liability</h2>
    <p>You are solely responsible for the content of your ads and the accuracy of any claims made therein. By submitting an ad, you confirm that:</p>
    <ul>
        <li>You have all necessary rights, licences, and permissions for any images, videos, logos, or trademarks included in the ad;</li>
        <li>The ad does not infringe any third-party intellectual property rights;</li>
        <li>All claims in the ad are truthful and substantiated;</li>
        <li>The ad complies with all applicable Nigerian laws and regulations.</li>
    </ul>
    <p>You agree to indemnify and hold Orderer harmless from any claims, penalties, damages, or expenses (including legal fees) arising from your ad content or your violation of this Ads Policy.</p>

    <h2 id="changes">12. Changes to This Policy</h2>
    <p>Orderer may update this Ads Policy from time to time. Material changes will be communicated to sellers via email or a notice in the Seller Dashboard at least 7 days before taking effect. Continued use of Orderer Ads after the effective date of a change constitutes acceptance of the updated policy.</p>
    <p>Changes to ad pricing or commission rates will be communicated with at least 30 days' advance notice, consistent with the Seller Terms.</p>

    <h2 id="contact">13. Contact</h2>
    <p>For advertising enquiries: <a href="mailto:sellers@ordererweb.com">sellers@ordererweb.com</a></p>
    <p>For ad disputes or credit requests: open a support ticket from your Seller Dashboard or email us with your ad ID.</p>

</div>

@include('layouts.storefront.footer')
<script src="{{ asset('js/jquery/jquery-2.2.4.min.js') }}"></script>
<script src="{{ asset('js/bootstrap.min.js') }}"></script>
<script src="{{ asset('js/classy-nav.min.js') }}"></script>
<script src="{{ asset('js/active.js') }}"></script>
</body>
</html>