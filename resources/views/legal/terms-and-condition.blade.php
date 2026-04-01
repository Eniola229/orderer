<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Terms and Conditions — Orderer</title>
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
        hr { border-color: #eee; margin: 36px 0; }
    </style>
</head>
<body>

@auth('web')@include('layouts.storefront.header-auth')
@else@include('layouts.storefront.header-guest')@endauth

<div class="breadcumb_area bg-img" style="background-image:url({{ asset('img/bg-img/breadcumb.jpeg') }});">
    <div class="container h-100"><div class="row h-100 align-items-center"><div class="col-12">
        <div class="page-title text-center"><h2>Terms and Conditions</h2></div>
    </div></div></div>
</div>

<div class="legal-wrap">

    <h1>Terms and Conditions</h1>
    <p class="meta">Last updated: {{ date('F d, Y') }} &nbsp;·&nbsp; Effective immediately upon account registration</p>

    <div class="legal-toc">
        <p>Table of Contents</p>
        <ol>
            <li><a href="#acceptance">Acceptance of Terms</a></li>
            <li><a href="#definitions">Definitions</a></li>
            <li><a href="#eligibility">Eligibility</a></li>
            <li><a href="#accounts">User Accounts</a></li>
            <li><a href="#marketplace">Marketplace Rules</a></li>
            <li><a href="#payments">Payments and Escrow</a></li>
            <li><a href="#fees">Fees and Commission</a></li>
            <li><a href="#prohibited">Prohibited Items and Conduct</a></li>
            <li><a href="#ip">Intellectual Property</a></li>
            <li><a href="#liability">Limitation of Liability</a></li>
            <li><a href="#disputes">Dispute Resolution</a></li>
            <li><a href="#termination">Termination</a></li>
            <li><a href="#governing">Governing Law</a></li>
            <li><a href="#changes">Changes to Terms</a></li>
            <li><a href="#contact">Contact Us</a></li>
        </ol>
    </div>

    <div class="highlight-box">
        <p>⚠ Please read these Terms carefully. By creating an account or using Orderer, you agree to be bound by these Terms and all applicable laws of the Federal Republic of Nigeria.</p>
    </div>

    <h2 id="acceptance">1. Acceptance of Terms</h2>
    <p>Welcome to Orderer ("Platform", "we", "us", or "our"), an online marketplace operated in the Federal Republic of Nigeria. These Terms and Conditions ("Terms") govern your access to and use of the Orderer website, mobile applications, and all related services.</p>
    <p>By registering an account, accessing, or using our Platform in any way, you confirm that you have read, understood, and agreed to be bound by these Terms, our Privacy Policy, and any additional guidelines or policies referenced herein. If you do not agree to these Terms, you must immediately stop using the Platform.</p>

    <h2 id="definitions">2. Definitions</h2>
    <ul>
        <li><strong>"Buyer"</strong> means any registered user who purchases goods, services, or properties through the Platform.</li>
        <li><strong>"Seller"</strong> means any approved registered user who lists and sells goods, services, or properties through the Platform.</li>
        <li><strong>"Listing"</strong> means any product, service, or property advertised for sale on the Platform.</li>
        <li><strong>"Transaction"</strong> means any completed purchase and sale between a Buyer and a Seller facilitated by the Platform.</li>
        <li><strong>"Escrow"</strong> means the payment holding mechanism through which Orderer temporarily holds funds paid by a Buyer until the Buyer confirms delivery.</li>
        <li><strong>"Wallet"</strong> means the digital balance maintained by Orderer on behalf of a registered user, denominated in Nigerian Naira (₦).</li>
        <li><strong>"NGN" or "₦"</strong> means Nigerian Naira, the official currency of the Federal Republic of Nigeria and the sole currency used on this Platform.</li>
        <li><strong>"Commission"</strong> means the fee charged by Orderer on each completed sale as set out in Section 7.</li>
        <li><strong>"KYC"</strong> means Know Your Customer — the identity verification process required for certain account activities.</li>
    </ul>

    <h2 id="eligibility">3. Eligibility</h2>
    <p>You must be at least 18 years of age to create an account and use the Platform. By registering, you represent and warrant that:</p>
    <ul>
        <li>You are at least 18 years old;</li>
        <li>You have the legal capacity to enter into binding contracts under Nigerian law;</li>
        <li>You are not prohibited from using the Platform under any applicable law;</li>
        <li>All information you provide during registration and thereafter is accurate, current, and complete;</li>
        <li>You will maintain the accuracy of such information and promptly update it when necessary.</li>
    </ul>
    <p>Orderer reserves the right to refuse access to, or terminate the account of, any user at any time for any reason, including failure to meet eligibility requirements.</p>

    <h2 id="accounts">4. User Accounts</h2>
    <h3>4.1 Registration</h3>
    <p>To access most features of the Platform, you must register for an account. You agree to provide accurate, complete information and to keep your account details up to date. You are responsible for maintaining the confidentiality of your account credentials and for all activities that occur under your account.</p>

    <h3>4.2 Seller Verification</h3>
    <p>Sellers are required to submit identification documents and undergo an approval process before their listings become publicly visible. Orderer reserves the right to approve or reject any seller application without providing reasons. Approved sellers must comply with all applicable Nigerian business and tax regulations.</p>

    <h3>4.3 Account Security</h3>
    <p>You must immediately notify us at <a href="mailto:support@ordererweb.shop">support@ordererweb.shop</a> if you suspect any unauthorised access to your account. Orderer will not be liable for any loss or damage arising from your failure to comply with this obligation.</p>

    <h2 id="marketplace">5. Marketplace Rules</h2>
    <h3>5.1 Seller Obligations</h3>
    <ul>
        <li>List only items you have the legal right to sell;</li>
        <li>Provide accurate, complete, and non-misleading descriptions, images, and pricing in Nigerian Naira (₦);</li>
        <li>Fulfil orders promptly and ship within the stated delivery timeframe;</li>
        <li>Respond to buyer enquiries within 48 hours;</li>
        <li>Comply with all applicable Nigerian consumer protection, trade, and tax laws.</li>
    </ul>

    <h3>5.2 Buyer Obligations</h3>
    <ul>
        <li>Purchase only for lawful purposes;</li>
        <li>Provide accurate delivery information;</li>
        <li>Confirm delivery within 7 days of receiving goods — failure to do so will result in automatic escrow release to the Seller;</li>
        <li>Not abuse the dispute or refund process.</li>
    </ul>

    <h3>5.3 Orderer's Role</h3>
    <p>Orderer is a marketplace facilitator and is not a party to any transaction between Buyers and Sellers. We do not own, inspect, or guarantee any listings. Our role is limited to providing the Platform infrastructure, payment escrow, and dispute mediation.</p>

    <h2 id="payments">6. Payments and Escrow</h2>
    <h3>6.1 Currency</h3>
    <p>All transactions on Orderer are conducted in Nigerian Naira (₦ / NGN). Prices displayed are in Naira unless otherwise stated.</p>

    <h3>6.2 Payment Processing</h3>
    <p>Payments are processed through Korapay, a licensed payment service provider. By making a payment on the Platform, you agree to Korapay's terms of service. Orderer does not store card details.</p>

    <h3>6.3 Wallet</h3>
    <p>Users may maintain a Wallet balance on the Platform denominated in Nigerian Naira. Wallet funds may be used to pay for purchases, fund advertising, or receive earnings. Wallets do not accrue interest. Orderer reserves the right to freeze wallets suspected of fraudulent activity pending investigation.</p>

    <h3>6.4 Escrow Mechanism</h3>
    <p>When a Buyer places an order, payment is held in escrow by Orderer. Funds are released to the Seller only when:</p>
    <ul>
        <li>The Buyer confirms delivery of the order; or</li>
        <li>Seven (7) days have elapsed from the date of shipment without a dispute being raised by the Buyer, in which case funds are automatically released.</li>
    </ul>
    <p>If a dispute is raised, funds remain in escrow until the dispute is resolved by Orderer's support team.</p>

    <h3>6.5 Seller Withdrawals</h3>
    <p>Sellers may withdraw available wallet balances to their verified Nigerian bank account. Withdrawals are subject to a minimum of ₦5,000 and are processed within 1–3 business days. Orderer reserves the right to delay withdrawals for security checks.</p>

    <h2 id="fees">7. Fees and Commission</h2>
    <p>Orderer charges the following fees:</p>
    <ul>
        <li><strong>Seller Commission:</strong> A percentage of each completed transaction, ranging from 5% to 15% depending on the product category, automatically deducted before seller earnings are released from escrow.</li>
        <li><strong>Advertising Fees:</strong> Sellers who run promotional ads are charged from their Ads Balance based on the selected ad type, placement, and duration. Ad rates are published on the Platform and subject to change.</li>
        <li><strong>Withdrawal Fees:</strong> No withdrawal fee is currently charged, though this may change with 30 days' notice.</li>
    </ul>
    <p>All fees are inclusive of applicable taxes where required by Nigerian law.</p>

    <h2 id="prohibited">8. Prohibited Items and Conduct</h2>
    <h3>8.1 Prohibited Items</h3>
    <p>The following may not be listed or sold on Orderer under any circumstances:</p>
    <ul>
        <li>Firearms, ammunition, explosives, or any weapons regulated under Nigerian law;</li>
        <li>Illegal drugs, narcotics, or controlled substances;</li>
        <li>Counterfeit, fake, or replica products of any kind;</li>
        <li>Stolen property or goods of unclear provenance;</li>
        <li>Human organs, human tissue, or human trafficking services;</li>
        <li>Child sexual abuse material (CSAM) or any content exploiting minors;</li>
        <li>Wild animals or protected species under CITES;</li>
        <li>Hazardous materials without proper certification;</li>
        <li>Financial instruments, currency, or investment products without regulatory approval;</li>
        <li>Any items prohibited under the laws of the Federal Republic of Nigeria.</li>
    </ul>

    <h3>8.2 Prohibited Conduct</h3>
    <ul>
        <li>Misrepresenting products, services, or identity;</li>
        <li>Manipulating prices, reviews, or ratings;</li>
        <li>Circumventing the Platform's payment system;</li>
        <li>Harassing, threatening, or abusing other users or Orderer staff;</li>
        <li>Using the Platform for money laundering or financing of terrorism;</li>
        <li>Creating multiple accounts to evade restrictions;</li>
        <li>Scraping, crawling, or using automated tools without written permission.</li>
    </ul>
    <p>Violation of these rules may result in immediate account suspension, fund freezing, and/or referral to law enforcement authorities.</p>

    <h2 id="ip">9. Intellectual Property</h2>
    <p>All content on the Platform including but not limited to logos, trademarks, software, text, images, and design is the property of Orderer or its licensors and is protected by Nigerian and international intellectual property laws.</p>
    <p>By uploading content to the Platform (product images, descriptions, etc.), you grant Orderer a non-exclusive, royalty-free, worldwide licence to use, display, and reproduce that content in connection with the operation and promotion of the Platform.</p>
    <p>You represent and warrant that you own or have the necessary rights to all content you upload and that such content does not infringe any third-party intellectual property rights.</p>

    <h2 id="liability">10. Limitation of Liability</h2>
    <p>To the maximum extent permitted by Nigerian law:</p>
    <ul>
        <li>Orderer provides the Platform on an "as is" and "as available" basis without warranty of any kind;</li>
        <li>Orderer is not liable for any indirect, incidental, special, or consequential damages arising from your use of the Platform;</li>
        <li>Orderer's total aggregate liability to you shall not exceed the total fees paid by you to Orderer in the 3 months preceding the event giving rise to the claim;</li>
        <li>Orderer is not responsible for the quality, safety, legality, or availability of items listed by Sellers.</li>
    </ul>

    <h2 id="disputes">11. Dispute Resolution</h2>
    <h3>11.1 Buyer–Seller Disputes</h3>
    <p>If a dispute arises between a Buyer and a Seller, both parties must first attempt to resolve the matter directly. If no resolution is reached within 5 business days, either party may open a formal dispute ticket through the Platform. Orderer's support team will review the evidence submitted and make a final, binding decision within 10 business days.</p>

    <h3>11.2 Disputes with Orderer</h3>
    <p>Any dispute, claim, or controversy arising out of or relating to these Terms or the Platform shall first be subject to good-faith negotiation. If unresolved, the matter shall be referred to arbitration under the Arbitration and Conciliation Act, Cap. A18, Laws of the Federation of Nigeria 2004, with the seat of arbitration in Lagos, Nigeria.</p>

    <h3>11.3 Governing Law</h3>
    <p>These Terms are governed by and construed in accordance with the laws of the Federal Republic of Nigeria.</p>

    <h2 id="termination">12. Termination</h2>
    <p>Orderer may suspend or terminate your account at any time, with or without notice, if we believe you have violated these Terms or applicable law. Upon termination:</p>
    <ul>
        <li>Your access to the Platform ceases immediately;</li>
        <li>Any pending orders must be fulfilled or cancelled in accordance with Orderer's policies;</li>
        <li>Wallet balances will be settled after deducting any amounts owed to Orderer, subject to our investigation processes;</li>
        <li>You may request account deletion by contacting <a href="mailto:support@ordererweb.shop">support@ordererweb.shop</a>.</li>
    </ul>

    <h2 id="governing">13. Governing Law</h2>
    <p>These Terms and your use of the Platform are governed exclusively by the laws of the Federal Republic of Nigeria, including but not limited to the Consumer Protection Framework issued by the Federal Competition and Consumer Protection Commission (FCCPC), the Nigeria Data Protection Regulation (NDPR), the Cybercrimes (Prohibition, Prevention, etc.) Act 2015, and all other applicable Nigerian statutes and regulations.</p>

    <h2 id="changes">14. Changes to Terms</h2>
    <p>Orderer reserves the right to modify these Terms at any time. We will notify registered users via email and/or a prominent notice on the Platform at least 14 days before material changes take effect. Continued use of the Platform after the effective date constitutes acceptance of the revised Terms.</p>

    <h2 id="contact">15. Contact Us</h2>
    <p>For questions about these Terms, please contact us:</p>
    <ul>
        <li>Email: <a href="mailto:legal@ordererweb.shop">legal@ordererweb.shop</a></li>
        <li>Support: <a href="{{ route('contact') }}">ordererweb.shop/contact</a></li>
        <li>Address: Lagos, Nigeria</li>
    </ul>

</div>

@include('layouts.storefront.footer')
<script src="{{ asset('js/jquery/jquery-2.2.4.min.js') }}"></script>
<script src="{{ asset('js/bootstrap.min.js') }}"></script>
<script src="{{ asset('js/classy-nav.min.js') }}"></script>
<script src="{{ asset('js/active.js') }}"></script>
</body>
</html>