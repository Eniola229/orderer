<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Privacy Policy — Orderer</title>
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
        .legal-toc a:hover{text-decoration:underline;}
        .highlight-box{background:#D5F5E3;border-radius:8px;padding:16px 20px;margin:20px 0;}
        .highlight-box p{margin:0;font-size:14px;color:#1E8449;font-weight:600;}
        hr{border-color:#eee;margin:36px 0;}
    </style>
</head>
<body>

@auth('web')@include('layouts.storefront.header-auth')
@else@include('layouts.storefront.header-guest')@endauth

<div class="breadcumb_area bg-img" style="background-image:url({{ asset('img/bg-img/breadcumb.jpeg') }});">
    <div class="container h-100"><div class="row h-100 align-items-center"><div class="col-12">
        <div class="page-title text-center"><h2>Privacy Policy</h2></div>
    </div></div></div>
</div>

<div class="legal-wrap">

    <h1>Privacy Policy</h1>
    <p class="meta">Last updated: {{ date('F d, Y') }} &nbsp;·&nbsp; Compliant with the Nigeria Data Protection Regulation (NDPR) 2019</p>

    <div class="highlight-box">
        <p>🔒 Orderer is committed to protecting your personal data in accordance with the Nigeria Data Protection Regulation (NDPR) and the Nigerian Data Protection Act 2023.</p>
    </div>

    <div class="legal-toc">
        <p>Table of Contents</p>
        <ol>
            <li><a href="#who">Who We Are</a></li>
            <li><a href="#data-we-collect">Data We Collect</a></li>
            <li><a href="#how-we-collect">How We Collect Data</a></li>
            <li><a href="#why-we-use">Why We Use Your Data</a></li>
            <li><a href="#legal-basis">Legal Basis for Processing</a></li>
            <li><a href="#sharing">Who We Share Your Data With</a></li>
            <li><a href="#retention">Data Retention</a></li>
            <li><a href="#security">Data Security</a></li>
            <li><a href="#rights">Your Rights</a></li>
            <li><a href="#cookies">Cookies</a></li>
            <li><a href="#children">Children's Privacy</a></li>
            <li><a href="#changes">Changes to This Policy</a></li>
            <li><a href="#contact">Contact and Complaints</a></li>
        </ol>
    </div>

    <h2 id="who">1. Who We Are</h2>
    <p>Orderer ("we", "us", "our") is an online marketplace platform headquartered in Lagos, Nigeria. We act as a Data Controller in respect of personal data collected through our Platform (website and mobile application).</p>
    <p>Our Data Protection Officer can be reached at <a href="mailto:dpo@ordererweb.shop">dpo@ordererweb.shop</a>.</p>

    <h2 id="data-we-collect">2. Data We Collect</h2>
    <h3>2.1 Information You Provide</h3>
    <ul>
        <li><strong>Account data:</strong> name, email address, phone number, password (encrypted), date of birth;</li>
        <li><strong>Seller data:</strong> business name, business address, CAC registration number, BVN or NIN (for KYC), bank account details;</li>
        <li><strong>Listing data:</strong> product descriptions, images, videos, pricing;</li>
        <li><strong>Transaction data:</strong> order details, delivery addresses, payment references;</li>
        <li><strong>Communications:</strong> support tickets, messages, reviews, and ratings.</li>
    </ul>

    <h3>2.2 Automatically Collected Data</h3>
    <ul>
        <li>IP address, browser type, operating system, device identifiers;</li>
        <li>Pages visited, time spent, click patterns, search queries;</li>
        <li>Cookie data (see Section 10).</li>
    </ul>

    <h3>2.3 Third-Party Data</h3>
    <p>We may receive data about you from payment processors, identity verification services, and fraud prevention providers.</p>

    <h2 id="how-we-collect">3. How We Collect Data</h2>
    <ul>
        <li>Directly from you during registration, checkout, and profile management;</li>
        <li>Automatically through cookies and similar tracking technologies;</li>
        <li>From payment and logistics partners processing your transactions;</li>
        <li>From identity verification providers during seller onboarding.</li>
    </ul>

    <h2 id="why-we-use">4. Why We Use Your Data</h2>
    <p>We process your personal data for the following purposes:</p>
    <ul>
        <li>To create and manage your account;</li>
        <li>To process orders, payments, and refunds;</li>
        <li>To verify your identity and prevent fraud (KYC/AML compliance);</li>
        <li>To send you transactional communications (order confirmations, shipping updates);</li>
        <li>To send marketing communications where you have given consent or where permitted by law;</li>
        <li>To personalise your experience and show relevant ads;</li>
        <li>To improve the Platform through analytics;</li>
        <li>To comply with Nigerian legal and regulatory obligations;</li>
        <li>To resolve disputes and enforce our Terms.</li>
    </ul>

    <h2 id="legal-basis">5. Legal Basis for Processing</h2>
    <p>Under the NDPR and Nigerian Data Protection Act 2023, we rely on the following lawful bases:</p>
    <ul>
        <li><strong>Contract performance:</strong> processing necessary to fulfil our obligations to you;</li>
        <li><strong>Legal obligation:</strong> where processing is required under Nigerian law (e.g., EFCC, CBN, FIRS regulations);</li>
        <li><strong>Legitimate interests:</strong> fraud prevention, platform security, and analytics;</li>
        <li><strong>Consent:</strong> marketing emails, cookies (you may withdraw consent at any time).</li>
    </ul>

    <h2 id="sharing">6. Who We Share Your Data With</h2>
    <p>We share your data only where necessary, with:</p>
    <ul>
        <li><strong>Sellers:</strong> your delivery address and order details to fulfil your purchase;</li>
        <li><strong>Buyers:</strong> seller business name and contact for order purposes;</li>
        <li><strong>Payment Processors:</strong> Korapay, to facilitate transactions;</li>
        <li><strong>Shipping Partners:</strong> Shipbubble and courier partners, for delivery fulfilment;</li>
        <li><strong>Identity Verification:</strong> KYC providers as required for seller onboarding;</li>
        <li><strong>Cloud Services:</strong> (media storage services), (email delivery systems);</li>
        <li><strong>Analytics:</strong> anonymised data with analytics providers;</li>
        <li><strong>Law Enforcement:</strong> Nigerian authorities (EFCC, NFIU, Police) where required by law.</li>
    </ul>
    <p>We do not sell your personal data to third parties.</p>

    <h2 id="retention">7. Data Retention</h2>
    <p>We retain your personal data for as long as necessary to fulfil the purposes for which it was collected, including:</p>
    <ul>
        <li><strong>Active accounts:</strong> for the duration of your account plus 2 years after closure;</li>
        <li><strong>Transaction records:</strong> 7 years, as required by FIRS and Nigerian tax law;</li>
        <li><strong>KYC documents:</strong> 5 years after the end of the business relationship, per EFCC requirements;</li>
        <li><strong>Marketing data:</strong> until you withdraw consent or 3 years of inactivity.</li>
    </ul>

    <h2 id="security">8. Data Security</h2>
    <p>We implement appropriate technical and organisational security measures including:</p>
    <ul>
        <li>TLS/HTTPS encryption for all data in transit;</li>
        <li>Bcrypt hashing for passwords;</li>
        <li>Role-based access controls limiting staff access to personal data;</li>
        <li>Regular security audits and penetration testing;</li>
        <li>Incident response procedures in compliance with NDPR breach notification requirements.</li>
    </ul>
    <p>In the event of a data breach, we will notify affected users and the Nigeria Data Protection Commission (NDPC) within 72 hours where required.</p>

    <h2 id="rights">9. Your Rights</h2>
    <p>Under the Nigerian Data Protection Act 2023, you have the following rights:</p>
    <ul>
        <li><strong>Right of access:</strong> request a copy of personal data we hold about you;</li>
        <li><strong>Right to rectification:</strong> correct inaccurate or incomplete data;</li>
        <li><strong>Right to erasure:</strong> request deletion of your data (subject to legal obligations);</li>
        <li><strong>Right to data portability:</strong> receive your data in a structured, machine-readable format;</li>
        <li><strong>Right to object:</strong> object to processing based on legitimate interests;</li>
        <li><strong>Right to withdraw consent:</strong> at any time, without affecting prior processing.</li>
    </ul>
    <p>To exercise any of these rights, contact us at <a href="mailto:dpo@ordererweb.shop">dpo@ordererweb.shop</a>. We will respond within 30 days.</p>

    <h2 id="cookies">10. Cookies</h2>
    <p>We use cookies and similar technologies to enhance your experience. For full details, please see our <a href="{{ route('legal.cookies') }}">Cookie Policy</a>.</p>

    <h2 id="children">11. Children's Privacy</h2>
    <p>Our Platform is not directed at persons under 18 years of age. We do not knowingly collect personal data from children. If you believe a child has provided us with personal data, please contact us immediately at <a href="mailto:dpo@ordererweb.shop">dpo@ordererweb.shop</a> and we will delete it promptly.</p>

    <h2 id="changes">12. Changes to This Policy</h2>
    <p>We may update this Privacy Policy periodically. We will notify you of material changes via email or a prominent notice on the Platform. Continued use of the Platform after notification constitutes acceptance of the updated Policy.</p>

    <h2 id="contact">13. Contact and Complaints</h2>
    <p>For privacy queries or to exercise your rights, contact our Data Protection Officer:</p>
    <ul>
        <li>Email: <a href="mailto:dpo@ordererweb.shop">dpo@ordererweb.shop</a></li>
        <li>Address: Lagos, Nigeria</li>
    </ul>
    <p>If you are not satisfied with our response, you have the right to lodge a complaint with the Nigeria Data Protection Commission (NDPC) at <a href="https://ndpc.gov.ng" target="_blank">ndpc.gov.ng</a>.</p>

</div>

@include('layouts.storefront.footer')
<script src="{{ asset('js/jquery/jquery-2.2.4.min.js') }}"></script>
<script src="{{ asset('js/bootstrap.min.js') }}"></script>
<script src="{{ asset('js/classy-nav.min.js') }}"></script>
<script src="{{ asset('js/active.js') }}"></script>
</body>
</html>