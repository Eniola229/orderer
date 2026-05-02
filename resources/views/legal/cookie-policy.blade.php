<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Cookie Policy — Orderer</title>
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
        table{width:100%;border-collapse:collapse;margin:16px 0;}
        th,td{padding:10px 14px;text-align:left;border:1px solid #eee;font-size:14px;}
        th{background:#f8f8f8;font-weight:700;color:#333;}
        tr:nth-child(even) td{background:#fafafa;}
    </style>
</head>
<body>

@auth('web')@include('layouts.storefront.header-auth')@else @include('layouts.storefront.header-guest')@endauth
@include('layouts.storefront.cart-sidebar')
@include('layouts.partials.alerts')

<div class="breadcumb_area bg-img" style="background-image:url({{ asset('img/bg-img/breadcumb.jpeg') }});">
    <div class="container h-100"><div class="row h-100 align-items-center"><div class="col-12">
        <div class="page-title text-center"><h2>Cookie Policy</h2></div>
    </div></div></div>
</div>

<div class="legal-wrap">

    <h1>Cookie Policy</h1>
    <p class="meta">Last updated: {{ date('F d, Y') }}</p>

    <div class="highlight-box">
        <p>🍪 This Cookie Policy explains how Orderer uses cookies and similar tracking technologies when you visit our Platform.</p>
    </div>

    <h2>1. What Are Cookies?</h2>
    <p>Cookies are small text files placed on your device (computer, smartphone, tablet) by a website when you visit it. They are widely used to make websites work efficiently, improve user experience, and provide information to website owners.</p>
    <p>Similar technologies include web beacons, pixel tags, and local storage, which function in a comparable way to cookies. This policy covers all such technologies.</p>

    <h2>2. How We Use Cookies</h2>
    <p>Orderer uses cookies for the following purposes:</p>

    <table>
        <thead>
            <tr>
                <th>Cookie Type</th>
                <th>Purpose</th>
                <th>Can You Opt Out?</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><strong>Essential / Strictly Necessary</strong></td>
                <td>Required for the Platform to function. Includes session cookies, login authentication tokens, CSRF protection, and shopping cart data.</td>
                <td>No — the Platform cannot function without these</td>
            </tr>
            <tr>
                <td><strong>Performance / Analytics</strong></td>
                <td>Help us understand how users interact with the Platform — pages visited, time on site, errors encountered. Data is aggregated and anonymised.</td>
                <td>Yes — via cookie settings</td>
            </tr>
            <tr>
                <td><strong>Functional</strong></td>
                <td>Remember your preferences such as language, currency (NGN), and login state to improve your experience.</td>
                <td>Yes — but this may affect functionality</td>
            </tr>
            <tr>
                <td><strong>Advertising / Targeting</strong></td>
                <td>Used to show you relevant ads within the Platform (sponsored products, banner ads) and to measure ad performance for sellers.</td>
                <td>Yes — via cookie settings</td>
            </tr>
        </tbody>
    </table>

    <h2>3. Specific Cookies We Use</h2>

    <table>
        <thead>
            <tr>
                <th>Cookie Name</th>
                <th>Type</th>
                <th>Duration</th>
                <th>Purpose</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><code>orderer_session</code></td>
                <td>Essential</td>
                <td>Session</td>
                <td>Maintains your login session</td>
            </tr>
            <tr>
                <td><code>XSRF-TOKEN</code></td>
                <td>Essential</td>
                <td>Session</td>
                <td>Security — prevents cross-site request forgery</td>
            </tr>
            <tr>
                <td><code>cart_session</code></td>
                <td>Essential</td>
                <td>7 days</td>
                <td>Saves your shopping cart between visits</td>
            </tr>
            <tr>
                <td><code>remember_web_*</code></td>
                <td>Functional</td>
                <td>30 days</td>
                <td>"Remember Me" persistent login</td>
            </tr>
            <tr>
                <td><code>_ga</code></td>
                <td>Analytics</td>
                <td>2 years</td>
                <td>Google Analytics — distinguishes users</td>
            </tr>
            <tr>
                <td><code>_gid</code></td>
                <td>Analytics</td>
                <td>24 hours</td>
                <td>Google Analytics — stores page view info</td>
            </tr>
            <tr>
                <td><code>ttq_*</code></td>
                <td>Advertising</td>
                <td>13 months</td>
                <td>TikTok Pixel — ad performance tracking</td>
            </tr>
            <tr>
                <td><code>ord_prefs</code></td>
                <td>Functional</td>
                <td>1 year</td>
                <td>Stores your display preferences</td>
            </tr>
        </tbody>
    </table>

    <h2>4. Third-Party Cookies</h2>
    <p>Some cookies on our Platform are set by third-party services we use:</p>
    <ul>
        <li><strong>Korapay</strong> — payment processing cookies set during checkout;</li>
        <li><strong>TikTok Pixel</strong> — advertising attribution;</li>
        <li><strong>Cloudinary</strong> — media delivery optimisation;</li>
        <li><strong>Brevo (formerly Sendinblue)</strong> — email marketing performance tracking.</li>
    </ul>
    <p>We do not control these third-party cookies. Please refer to each provider's privacy and cookie policy for details.</p>

    <h2>5. Managing Cookies</h2>
    <p>You can control and manage cookies in several ways:</p>
    <h3>5.1 Browser Settings</h3>
    <p>Most browsers allow you to view, block, and delete cookies through their settings. Note that blocking essential cookies will prevent the Platform from functioning correctly.</p>
    <ul>
        <li><a href="https://support.google.com/chrome/answer/95647" target="_blank">Google Chrome</a></li>
        <li><a href="https://support.mozilla.org/en-US/kb/enable-and-disable-cookies-website-preferences" target="_blank">Mozilla Firefox</a></li>
        <li><a href="https://support.apple.com/en-gb/guide/safari/sfri11471/mac" target="_blank">Safari</a></li>
        <li><a href="https://support.microsoft.com/en-us/windows/delete-and-manage-cookies" target="_blank">Microsoft Edge</a></li>
    </ul>

    <h3>5.2 Opt-Out Tools</h3>
    <ul>
        <li>Google Analytics: <a href="https://tools.google.com/dlpage/gaoptout" target="_blank">Google Analytics Opt-out Browser Add-on</a></li>
    </ul>

    <h2>6. Cookie Consent</h2>
    <p>On your first visit to Orderer, you will be shown a cookie consent banner. By clicking "Accept All", you consent to all cookie categories. You may choose to accept only essential cookies by clicking "Manage Preferences".</p>
    <p>You may withdraw or change your cookie consent at any time by clearing your browser cookies and revisiting the Platform, or by contacting us at <a href="mailto:privacy@ordererweb.com">privacy@ordererweb.com</a>.</p>

    <h2>7. Updates to This Policy</h2>
    <p>We may update this Cookie Policy from time to time. We will notify you of significant changes through the Platform. Continued use after the update date constitutes acceptance.</p>

    <h2>8. Contact</h2>
    <p>For cookie-related queries: <a href="mailto:privacy@ordererweb.com">privacy@ordererweb.com</a></p>

</div>

@include('layouts.storefront.footer')
<script src="{{ asset('js/jquery/jquery-2.2.4.min.js') }}"></script>
<script src="{{ asset('js/bootstrap.min.js') }}"></script>
<script src="{{ asset('js/classy-nav.min.js') }}"></script>
<script src="{{ asset('js/active.js') }}"></script>
</body>
</html>