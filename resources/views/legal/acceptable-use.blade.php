<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Acceptable Use Policy — Orderer</title>
    <link rel="icon" href="{{ asset('img/core-img/favicon.png') }}">
    <link rel="stylesheet" href="{{ asset('css/core-style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/orderer.css') }}">
    <style>
        .legal-wrap { max-width: 860px; margin: 0 auto; padding: 60px 24px 80px; }
        .legal-wrap h1 { font-size: 32px; font-weight: 800; color: #1a1a1a; margin-bottom: 6px; }
        .legal-wrap .meta { color: #888; font-size: 14px; margin-bottom: 40px; }
        .legal-wrap h2 { font-size: 20px; font-weight: 700; color: #1a1a1a; margin: 36px 0 12px; }
        .legal-wrap h3 { font-size: 16px; font-weight: 700; color: #333; margin: 24px 0 8px; }
        .legal-wrap p, .legal-wrap li { font-size: 15px; color: #444; line-height: 1.85; }
        .legal-wrap ul, .legal-wrap ol { padding-left: 22px; margin-bottom: 16px; }
        .legal-wrap li { margin-bottom: 6px; }
        .legal-wrap a { color: #2ECC71; }
        .legal-toc { background: #f8f8f8; border-left: 3px solid #2ECC71; padding: 20px 24px; border-radius: 0 8px 8px 0; margin-bottom: 40px; }
        .legal-toc p { font-weight: 700; margin-bottom: 8px; font-size: 14px; }
        .legal-toc ol { margin: 0; padding-left: 18px; }
        .legal-toc li { font-size: 13px; margin-bottom: 4px; }
        .legal-toc a { color: #2ECC71; text-decoration: none; }
        .legal-toc a:hover { text-decoration: underline; }
        .highlight-box { background: #D5F5E3; border-radius: 8px; padding: 16px 20px; margin: 20px 0; }
        .highlight-box p { margin: 0; font-size: 14px; color: #1E8449; font-weight: 600; }
        .warning-box { background: #FADBD8; border-radius: 8px; padding: 16px 20px; margin: 20px 0; border-left: 3px solid #E74C3C; }
        .warning-box p { margin: 0; font-size: 14px; color: #A93226; font-weight: 600; }
        hr { border-color: #eee; margin: 36px 0; }
    </style>
</head>
<body>

@auth('web')@include('layouts.storefront.header-auth')@else @include('layouts.storefront.header-guest')@endauth
@include('layouts.storefront.cart-sidebar')
@include('layouts.partials.alerts')
 
<div class="breadcumb_area bg-img" style="background-image:url({{ asset('img/bg-img/breadcumb.jpeg') }});">
    <div class="container h-100"><div class="row h-100 align-items-center"><div class="col-12">
        <div class="page-title text-center"><h2>Acceptable Use Policy</h2></div>
    </div></div></div>
</div>

<div class="legal-wrap">

    <h1>Acceptable Use Policy</h1>
    <p class="meta">Last updated: {{ date('F d, Y') }} &nbsp;·&nbsp; Applies to all users of the Orderer Platform</p>

    <div class="highlight-box">
        <p>✅ This Acceptable Use Policy ("AUP") describes what you may and may not do while using Orderer. It exists to keep the Platform safe, lawful, and trustworthy for every user.</p>
    </div>

    <div class="legal-toc">
        <p>Table of Contents</p>
        <ol>
            <li><a href="#overview">Overview</a></li>
            <li><a href="#permitted">Permitted Uses</a></li>
            <li><a href="#prohibited-content">Prohibited Content</a></li>
            <li><a href="#prohibited-conduct">Prohibited Conduct</a></li>
            <li><a href="#platform-integrity">Platform Integrity</a></li>
            <li><a href="#third-party">Third-Party Rights</a></li>
            <li><a href="#minors">Protection of Minors</a></li>
            <li><a href="#cybercrime">Cybercrime and Data Protection</a></li>
            <li><a href="#enforcement">Enforcement</a></li>
            <li><a href="#reporting-violations">Reporting Violations</a></li>
            <li><a href="#updates">Updates to This Policy</a></li>
        </ol>
    </div>

    <h2 id="overview">1. Overview</h2>
    <p>This Acceptable Use Policy supplements and forms part of Orderer's <a href="{{ route('legal.terms') }}">Terms and Conditions</a>. All users — whether buyers, sellers, or visitors — must comply with this Policy at all times. By accessing the Platform, you acknowledge that you have read and agree to this Policy.</p>
    <p>Orderer reserves the right to take any action it deems appropriate against users who violate this Policy, including account suspension, content removal, fund freezing, and referral to Nigerian law enforcement authorities.</p>

    <h2 id="permitted">2. Permitted Uses</h2>
    <p>You may use the Orderer Platform to:</p>
    <ul>
        <li>Browse, search, and purchase products, services, and properties listed by approved sellers;</li>
        <li>Register as a seller and list lawful goods, services, or properties for sale;</li>
        <li>Communicate with other users in connection with legitimate transactions;</li>
        <li>Leave honest reviews and ratings for completed transactions;</li>
        <li>Access your account dashboard, manage your wallet, and track orders;</li>
        <li>Run compliant advertising campaigns for your listed products or services;</li>
        <li>Share listings on social media for personal, non-commercial use.</li>
    </ul>

    <h2 id="prohibited-content">3. Prohibited Content</h2>
    <p>You may not upload, post, share, or transmit any content on the Platform that:</p>

    <h3>3.1 Illegal Content</h3>
    <ul>
        <li>Violates any applicable Nigerian law, including the Cybercrimes Act 2015, EFCC Act, MLPPA 2022, or any other statute;</li>
        <li>Promotes, facilitates, or glorifies criminal activity;</li>
        <li>Constitutes or facilitates money laundering, fraud, or financing of terrorism;</li>
        <li>Infringes on any intellectual property rights, including copyright, trademarks, patents, or trade secrets.</li>
    </ul>

    <h3>3.2 Harmful Content</h3>
    <ul>
        <li>Is defamatory, libellous, or injurious to the reputation of any person or organisation under Nigerian law;</li>
        <li>Is threatening, abusive, harassing, or intimidating;</li>
        <li>Incites violence, ethnic hatred, religious intolerance, or discrimination based on tribe, religion, gender, disability, or any other protected characteristic;</li>
        <li>Promotes self-harm, suicide, or eating disorders;</li>
        <li>Is obscene, pornographic, or sexually explicit — except lawful adult content where the Platform explicitly permits such a category, which it currently does not.</li>
    </ul>

    <h3>3.3 Child Safety</h3>
    <ul>
        <li>Contains child sexual abuse material (CSAM) of any kind — this will result in immediate referral to the Nigeria Police Force and INTERPOL;</li>
        <li>Sexualises, exploits, or endangers minors in any manner;</li>
        <li>Targets minors for commercial or sexual purposes.</li>
    </ul>

    <h3>3.4 Misleading Content</h3>
    <ul>
        <li>Contains false, inaccurate, or misleading product descriptions, images, or pricing;</li>
        <li>Impersonates any person, business, or organisation;</li>
        <li>Creates a false impression of affiliation with Orderer or any third party;</li>
        <li>Contains fake reviews, testimonials, or ratings.</li>
    </ul>

    <h2 id="prohibited-conduct">4. Prohibited Conduct</h2>
    <p>The following actions are strictly prohibited on Orderer:</p>

    <h3>4.1 Fraudulent Activity</h3>
    <ul>
        <li>Listing items you do not own or have no right to sell;</li>
        <li>Accepting payment without intending to fulfil the order;</li>
        <li>Using stolen or fraudulent payment credentials;</li>
        <li>Filing false disputes or claiming non-delivery of goods you have received;</li>
        <li>Creating multiple accounts to exploit referral bonuses or circumvent restrictions.</li>
    </ul>

    <h3>4.2 Market Manipulation</h3>
    <ul>
        <li>Artificially inflating prices through coordinated action with other sellers;</li>
        <li>Purchasing your own listings to inflate sales figures or ratings;</li>
        <li>Using fake accounts to leave positive reviews on your own listings;</li>
        <li>Soliciting or incentivising positive reviews from buyers;</li>
        <li>Retaliating against buyers who leave negative reviews.</li>
    </ul>

    <h3>4.3 Circumventing the Platform</h3>
    <ul>
        <li>Directing buyers to complete transactions outside of Orderer's payment system to avoid commissions or fees;</li>
        <li>Sharing contact information in listings for the purpose of off-platform sales;</li>
        <li>Using Orderer solely as a marketing channel while fulfilling orders externally.</li>
    </ul>

    <h3>4.4 Harassment and Abuse</h3>
    <ul>
        <li>Sending unsolicited commercial messages (spam) to other users;</li>
        <li>Repeatedly contacting a user after they have asked you to stop;</li>
        <li>Threatening, intimidating, or blackmailing other users or Orderer staff;</li>
        <li>Using offensive, abusive, or discriminatory language in communications.</li>
    </ul>

    <h2 id="platform-integrity">5. Platform Integrity</h2>
    <p>You may not:</p>
    <ul>
        <li>Attempt to gain unauthorised access to any part of the Platform, any other user's account, or Orderer's systems;</li>
        <li>Introduce viruses, malware, ransomware, or any other harmful code;</li>
        <li>Use automated scripts, bots, crawlers, or scraping tools without Orderer's prior written consent;</li>
        <li>Attempt to reverse-engineer, decompile, or disassemble any part of the Platform's software;</li>
        <li>Interfere with or disrupt the Platform's infrastructure, servers, or networks;</li>
        <li>Conduct or facilitate Distributed Denial of Service (DDoS) attacks;</li>
        <li>Exploit any bugs or vulnerabilities rather than reporting them responsibly to <a href="mailto:security@ordererweb.com">security@ordererweb.com</a>.</li>
    </ul>

    <div class="warning-box">
        <p>⚠ Attempting to hack or disrupt Orderer's systems is a criminal offence under the Cybercrimes (Prohibition, Prevention, etc.) Act 2015 and will be prosecuted.</p>
    </div>

    <h2 id="third-party">6. Third-Party Rights</h2>
    <p>You must respect the rights of third parties at all times. You may not:</p>
    <ul>
        <li>Upload content that infringes a third party's copyright, trademark, patent, trade secret, or other intellectual property right;</li>
        <li>Use another person's personal data without their consent or legal basis;</li>
        <li>List counterfeit goods bearing the brand or trademark of another entity without authorisation;</li>
        <li>Use Orderer to facilitate unauthorised use of licensed software, media, or digital content.</li>
    </ul>
    <p>Orderer will respond to valid intellectual property complaints from rights holders. If you believe your rights have been infringed, contact <a href="mailto:legal@ordererweb.com">legal@ordererweb.com</a>.</p>

    <h2 id="minors">7. Protection of Minors</h2>
    <p>Orderer takes the protection of minors extremely seriously. You must not:</p>
    <ul>
        <li>Allow a person under 18 years of age to use your account;</li>
        <li>List products or services that are age-restricted without appropriate verification mechanisms;</li>
        <li>Produce, distribute, or possess child sexual abuse material (CSAM) — this is a serious criminal offence under Nigerian law and will be immediately reported to law enforcement;</li>
        <li>Market products in a manner that primarily targets children without appropriate safeguards.</li>
    </ul>

    <h2 id="cybercrime">8. Cybercrime and Data Protection</h2>
    <p>You must comply with all applicable Nigerian cybercrime and data protection laws, including:</p>
    <ul>
        <li>The Cybercrimes (Prohibition, Prevention, etc.) Act 2015 — you must not engage in identity theft, phishing, hacking, or computer fraud;</li>
        <li>The Nigeria Data Protection Act 2023 and NDPR 2019 — you must not unlawfully collect, store, or process the personal data of other users;</li>
        <li>You must not use data obtained through the Platform for purposes beyond the transaction in which it was shared.</li>
    </ul>

    <h2 id="enforcement">9. Enforcement</h2>
    <p>Orderer monitors Platform activity and investigates reports of violations. Upon finding a violation, we may take any or all of the following actions at our sole discretion:</p>
    <ul>
        <li>Issue a warning;</li>
        <li>Remove offending content or listings;</li>
        <li>Temporarily suspend your account;</li>
        <li>Permanently terminate your account;</li>
        <li>Freeze your wallet balance pending investigation;</li>
        <li>Withhold payments;</li>
        <li>Report your conduct to the EFCC, Nigeria Police Force, NFIU, or any other competent Nigerian authority;</li>
        <li>Pursue civil legal action against you.</li>
    </ul>
    <p>Orderer is not obligated to give advance notice before taking enforcement action in serious cases.</p>

    <h2 id="reporting-violations">10. Reporting Violations</h2>
    <p>If you encounter a listing, message, or user that you believe violates this Policy or any applicable law, please report it:</p>
    <ul>
        <li>Use the "Report" button on any listing or message;</li>
        <li>Open a support ticket from your account dashboard;</li>
        <li>Email: <a href="mailto:trust@ordererweb.com">trust@ordererweb.com</a></li>
    </ul>
    <p>All reports are reviewed by our Trust and Safety team. We do not tolerate retaliation against users who report violations in good faith.</p>

    <h2 id="updates">11. Updates to This Policy</h2>
    <p>Orderer may update this Acceptable Use Policy from time to time to reflect changes in law, technology, or Platform operations. Updated versions will be published on the Platform with the effective date. Continued use of the Platform after such updates constitutes acceptance of the revised Policy.</p>

    <hr>
    <p style="font-size:14px;color:#888;">For questions about this Policy: <a href="mailto:legal@ordererweb.com">legal@ordererweb.com</a></p>

</div>

@include('layouts.storefront.footer')
<script src="{{ asset('js/jquery/jquery-2.2.4.min.js') }}"></script>
<script src="{{ asset('js/bootstrap.min.js') }}"></script>
<script src="{{ asset('js/classy-nav.min.js') }}"></script>
<script src="{{ asset('js/active.js') }}"></script>
</body>
</html>