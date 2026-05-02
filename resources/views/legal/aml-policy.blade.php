<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>AML Policy — Orderer</title>
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
        <div class="page-title text-center"><h2>AML &amp; CFT Policy</h2></div>
    </div></div></div>
</div>

<div class="legal-wrap">

    <h1>Anti-Money Laundering &amp; Counter-Financing of Terrorism Policy</h1>
    <p class="meta">Last updated: {{ date('F d, Y') }} &nbsp;·&nbsp; Compliant with EFCC Act 2004, MLPPA 2022, and CBN AML/CFT Regulations</p>

    <div class="highlight-box">
        <p>🛡 Orderer is committed to complying fully with all applicable Nigerian anti-money laundering (AML) and counter-financing of terrorism (CFT) laws and regulations. We do not tolerate the use of our Platform for financial crime of any kind.</p>
    </div>

    <div class="legal-toc">
        <p>Table of Contents</p>
        <ol>
            <li><a href="#purpose">Purpose and Scope</a></li>
            <li><a href="#legal-framework">Legal Framework</a></li>
            <li><a href="#kyc">Know Your Customer (KYC)</a></li>
            <li><a href="#monitoring">Transaction Monitoring</a></li>
            <li><a href="#red-flags">Red Flags and Suspicious Activity</a></li>
            <li><a href="#reporting">Reporting Obligations</a></li>
            <li><a href="#sanctions">Sanctions Screening</a></li>
            <li><a href="#pep">Politically Exposed Persons (PEPs)</a></li>
            <li><a href="#training">Staff Training</a></li>
            <li><a href="#record-keeping">Record Keeping</a></li>
            <li><a href="#user-obligations">User Obligations</a></li>
            <li><a href="#consequences">Consequences of Violations</a></li>
            <li><a href="#contact">Contact and Reporting</a></li>
        </ol>
    </div>

    <h2 id="purpose">1. Purpose and Scope</h2>
    <p>This Anti-Money Laundering and Counter-Financing of Terrorism ("AML/CFT") Policy sets out Orderer's framework for detecting, preventing, and reporting money laundering, terrorist financing, and related financial crimes.</p>
    <p>This Policy applies to:</p>
    <ul>
        <li>All users of the Orderer Platform — both Buyers and Sellers;</li>
        <li>All transactions processed through the Platform;</li>
        <li>All Orderer employees, contractors, and agents;</li>
        <li>All wallet, payment, and withdrawal activities.</li>
    </ul>

    <h2 id="legal-framework">2. Legal Framework</h2>
    <p>This Policy is designed to comply with the following Nigerian laws and regulations:</p>
    <ul>
        <li>Money Laundering (Prevention and Prohibition) Act 2022 (MLPPA);</li>
        <li>Terrorism (Prevention and Prohibition) Act 2022;</li>
        <li>Economic and Financial Crimes Commission (Establishment) Act 2004 (EFCC Act);</li>
        <li>Central Bank of Nigeria (CBN) AML/CFT Regulations for Financial Institutions;</li>
        <li>Nigerian Financial Intelligence Unit (NFIU) Guidelines;</li>
        <li>FATF (Financial Action Task Force) Recommendations as adopted by Nigeria;</li>
        <li>Cybercrimes (Prohibition, Prevention, etc.) Act 2015.</li>
    </ul>

    <h2 id="kyc">3. Know Your Customer (KYC)</h2>
    <h3>3.1 Buyer Verification</h3>
    <p>Buyers are required to provide a valid email address and phone number during registration. For transactions above defined thresholds or where risk indicators are triggered, additional identity verification may be required including:</p>
    <ul>
        <li>Government-issued photo ID (NIN slip, International Passport, Driver's Licence);</li>
        <li>Bank Verification Number (BVN);</li>
        <li>Proof of address.</li>
    </ul>

    <h3>3.2 Seller Verification</h3>
    <p>All Sellers undergo mandatory KYC before their accounts are approved. Required documents include:</p>
    <ul>
        <li>Individual sellers: valid NIN, BVN, or government-issued photo ID;</li>
        <li>Business sellers: CAC Certificate of Incorporation, Business Name registration, Tax Identification Number (TIN), and a director's valid ID;</li>
        <li>Bank account details linked to a verifiable Nigerian bank account.</li>
    </ul>

    <h3>3.3 Enhanced Due Diligence (EDD)</h3>
    <p>Enhanced due diligence is applied to sellers with high transaction volumes, those dealing in high-value categories (real estate, electronics above certain thresholds), and Politically Exposed Persons (PEPs). EDD may include source of funds verification and periodic account reviews.</p>

    <h2 id="monitoring">4. Transaction Monitoring</h2>
    <p>Orderer operates an automated transaction monitoring system that flags unusual patterns including but not limited to:</p>
    <ul>
        <li>Sudden significant increases in transaction volume or frequency;</li>
        <li>Multiple transactions just below reporting thresholds (structuring);</li>
        <li>Large cash-equivalent payments inconsistent with the user's profile;</li>
        <li>Transactions involving counterparties in high-risk jurisdictions;</li>
        <li>Wallet top-ups immediately followed by withdrawal to different bank accounts;</li>
        <li>Unusually high order values for low-risk product categories.</li>
    </ul>
    <p>Flagged transactions are manually reviewed by Orderer's compliance team. Accounts under review may be temporarily restricted from withdrawals pending investigation.</p>

    <h2 id="red-flags">5. Red Flags and Suspicious Activity</h2>
    <p>The following patterns are considered red flags and will trigger a Suspicious Activity Report (SAR) filing with the NFIU:</p>
    <ul>
        <li>A user provides false, inconsistent, or unverifiable identity information;</li>
        <li>A user is reluctant to provide KYC documents or provides altered documents;</li>
        <li>Transactions have no apparent legitimate commercial purpose;</li>
        <li>A user requests that transactions be split to avoid reporting thresholds;</li>
        <li>Multiple accounts are used to layer transactions;</li>
        <li>A user's transaction patterns are inconsistent with their stated business;</li>
        <li>A user matches a name on an international or domestic sanctions list;</li>
        <li>Payments are received from or sent to known high-risk jurisdictions.</li>
    </ul>

    <div class="warning-box">
        <p>⚠ If you are aware of or suspect any money laundering or terrorist financing activity on the Platform, you are legally obligated under the MLPPA 2022 to report it. Do not tip off the suspect. Contact us confidentially at <a href="mailto:compliance@ordererweb.com" style="color:#A93226;">compliance@ordererweb.com</a>.</p>
    </div>

    <h2 id="reporting">6. Reporting Obligations</h2>
    <p>Orderer is a Designated Non-Financial Business and Profession (DNFBP) under Nigerian law. We are required to:</p>
    <ul>
        <li>File Suspicious Transaction Reports (STRs) with the Nigerian Financial Intelligence Unit (NFIU) where we suspect money laundering or terrorist financing;</li>
        <li>File Currency Transaction Reports (CTRs) for cash-equivalent transactions above applicable thresholds;</li>
        <li>Cooperate fully with the EFCC, NFIU, CBN, and any other competent Nigerian authority in investigations;</li>
        <li>Maintain all records required to be kept under the MLPPA 2022.</li>
    </ul>
    <p>Orderer will not notify a user that a suspicious transaction report has been filed regarding their account — doing so ("tipping off") is a criminal offence under Nigerian law.</p>

    <h2 id="sanctions">7. Sanctions Screening</h2>
    <p>Orderer screens all users and transactions against:</p>
    <ul>
        <li>The NFIU designated entities list;</li>
        <li>The UN Security Council sanctions lists;</li>
        <li>The OFAC Specially Designated Nationals (SDN) list;</li>
        <li>EU consolidated sanctions list;</li>
        <li>Other applicable international sanctions regimes.</li>
    </ul>
    <p>Any user or transaction matching a sanctions list will be immediately blocked. The relevant authorities will be notified as required by law. Funds related to sanctioned entities will be frozen.</p>

    <h2 id="pep">8. Politically Exposed Persons (PEPs)</h2>
    <p>A Politically Exposed Person (PEP) is an individual who holds or has held a prominent public function — such as a head of state, minister, senior government official, judicial officer, military commander, or senior official of a political party — as well as their immediate family members and close associates.</p>
    <p>PEPs are not prohibited from using Orderer, but are subject to Enhanced Due Diligence (EDD) including:</p>
    <ul>
        <li>Senior management approval for account activation;</li>
        <li>Source of wealth and source of funds verification;</li>
        <li>Ongoing enhanced monitoring of transactions.</li>
    </ul>

    <h2 id="training">9. Staff Training</h2>
    <p>All Orderer employees with access to user data, financial systems, or compliance functions receive mandatory AML/CFT training upon joining and at least annually thereafter. Training covers Nigerian AML law, red flag identification, and internal reporting procedures.</p>

    <h2 id="record-keeping">10. Record Keeping</h2>
    <p>In compliance with the MLPPA 2022, Orderer retains:</p>
    <ul>
        <li>KYC documents and identity records for a minimum of 5 years after the end of the business relationship;</li>
        <li>Transaction records for a minimum of 5 years from the date of the transaction;</li>
        <li>Suspicious transaction reports and related correspondence for a minimum of 5 years.</li>
    </ul>
    <p>All records are stored securely and are available to competent authorities upon lawful request.</p>

    <h2 id="user-obligations">11. User Obligations</h2>
    <p>By using Orderer, all users agree to:</p>
    <ul>
        <li>Use the Platform only for lawful commercial purposes;</li>
        <li>Provide truthful and complete information at registration and upon request;</li>
        <li>Not use the Platform to launder money, finance terrorism, or evade taxes;</li>
        <li>Not structure transactions to circumvent reporting thresholds;</li>
        <li>Co-operate with Orderer's compliance processes, including providing additional documentation when requested.</li>
    </ul>

    <h2 id="consequences">12. Consequences of Violations</h2>
    <p>Users found to be in violation of this Policy or applicable AML/CFT laws will face:</p>
    <ul>
        <li>Immediate account suspension and freezing of all associated wallets;</li>
        <li>Permanent ban from the Platform;</li>
        <li>Mandatory reporting to the NFIU, EFCC, or other relevant Nigerian authorities;</li>
        <li>Forfeiture of any funds held on the Platform;</li>
        <li>Civil and criminal liability under Nigerian law, including prosecution under the MLPPA 2022 and EFCC Act.</li>
    </ul>

    <h2 id="contact">13. Contact and Reporting</h2>
    <p>To report suspicious activity or AML/CFT concerns confidentially:</p>
    <ul>
        <li>Email: <a href="mailto:compliance@ordererweb.web">compliance@ordererweb.com</a></li>
        <li>Address: Lagos, Nigeria</li>
    </ul>
    <p>External reports may also be made directly to:</p>
    <ul>
        <li>Nigerian Financial Intelligence Unit (NFIU): <a href="https://nfiu.gov.ng" target="_blank">nfiu.gov.ng</a></li>
        <li>Economic and Financial Crimes Commission (EFCC): <a href="https://efcc.gov.ng" target="_blank">efcc.gov.ng</a></li>
    </ul>

</div>

@include('layouts.storefront.footer')
<script src="{{ asset('js/jquery/jquery-2.2.4.min.js') }}"></script>
<script src="{{ asset('js/bootstrap.min.js') }}"></script>
<script src="{{ asset('js/classy-nav.min.js') }}"></script>
<script src="{{ asset('js/active.js') }}"></script>
</body>
</html>