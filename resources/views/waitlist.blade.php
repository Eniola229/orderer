<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Something's Coming — Orderer</title>
    <meta name="description" content="Orderer — Buy, sell and deliver anything, anywhere in the world." />
    <meta name="keywords" content="ecommerce Nigeria, buy online, sell online, orderer, marketplace, delivery" />
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('dashboard/assets/images/favicon.png') }}" />
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: #f7f7f5;
            color: #1a1a1a;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        nav {
            padding: 20px 40px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid #e8e8e4;
            background: #fff;
        }
        nav img { height: 32px; }
        nav a {
            font-size: 13px;
            color: #555;
            text-decoration: none;
            transition: color .2s;
        }
        nav a:hover { color: #1a1a1a; }

        main {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 60px 24px;
        }

        .container { max-width: 560px; width: 100%; }

        .eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 12px;
            font-weight: 600;
            letter-spacing: .08em;
            text-transform: uppercase;
            color: #2ECC71;
            margin-bottom: 28px;
        }
        .eyebrow span {
            display: inline-block;
            width: 24px;
            height: 2px;
            background: #2ECC71;
            border-radius: 2px;
        }

        h1 {
            font-size: clamp(32px, 6vw, 48px);
            font-weight: 700;
            line-height: 1.15;
            color: #111;
            margin-bottom: 10px;
            letter-spacing: -0.02em;
        }
        h1 em { font-style: normal; color: #2ECC71; }

        .africgem {
            font-size: 12px;
            color: #aaa;
            margin-bottom: 28px;
            letter-spacing: .01em;
        }
        .africgem strong { color: #777; font-weight: 600; }

        .description {
            font-size: 16px;
            line-height: 1.75;
            color: #555;
            margin-bottom: 36px;
        }
        .description strong { color: #1a1a1a; font-weight: 600; }

        .form-wrap {
            background: #fff;
            border: 1px solid #e8e8e4;
            border-radius: 14px;
            padding: 28px;
            margin-bottom: 28px;
        }
        .form-wrap > p {
            font-size: 14px;
            color: #777;
            margin-bottom: 16px;
        }

        .input-row { display: flex; gap: 10px; }

        .input-row input[type="email"] {
            flex: 1;
            height: 46px;
            padding: 0 16px;
            border: 1.5px solid #ddd;
            border-radius: 8px;
            font-size: 14px;
            color: #111;
            background: #fafafa;
            outline: none;
            transition: border-color .2s;
        }
        .input-row input[type="email"]:focus {
            border-color: #2ECC71;
            background: #fff;
        }
        .input-row input[type="email"]::placeholder { color: #aaa; }

        .input-row button {
            height: 46px;
            padding: 0 22px;
            background: #2ECC71;
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            white-space: nowrap;
            transition: background .2s, transform .1s;
            display: flex;
            align-items: center;
            gap: 8px;
            min-width: 110px;
            justify-content: center;
        }
        .input-row button:hover { background: #27AE60; }
        .input-row button:active { transform: scale(0.98); }
        .input-row button:disabled { opacity: .7; cursor: not-allowed; }

        .err-box {
            display: none;
            background: #fef2f2;
            border: 1px solid #fecaca;
            border-radius: 8px;
            padding: 10px 14px;
            font-size: 13px;
            color: #991b1b;
            margin-bottom: 14px;
        }

        @keyframes spin { to { transform: rotate(360deg); } }
        .spinner {
            width: 15px;
            height: 15px;
            border: 2px solid rgba(255,255,255,.35);
            border-top-color: #fff;
            border-radius: 50%;
            animation: spin .6s linear infinite;
            display: none;
            flex-shrink: 0;
        }

        .pills {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-bottom: 40px;
        }
        .pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 12px;
            color: #444;
            background: #fff;
            border: 1px solid #e0e0dc;
            border-radius: 20px;
            padding: 5px 12px;
        }
        .pill-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: #2ECC71;
            flex-shrink: 0;
        }

        .footer-note { font-size: 12px; color: #999; line-height: 1.6; }
        .footer-note a { color: #555; text-decoration: none; border-bottom: 1px solid #ddd; }
        .footer-note a:hover { color: #111; border-color: #999; }

        footer {
            padding: 20px 40px;
            border-top: 1px solid #e8e8e4;
            background: #fff;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
        }
        footer p { font-size: 12px; color: #aaa; }
        footer p strong { color: #777; font-weight: 600; }

        #toast {
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%) translateY(-20px);
            background: #111;
            color: #fff;
            padding: 14px 22px;
            border-radius: 10px;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 10px;
            opacity: 0;
            transition: opacity .3s ease, transform .3s ease;
            pointer-events: none;
            z-index: 9999;
            white-space: nowrap;
            box-shadow: 0 4px 24px rgba(0,0,0,.18);
        }
        #toast.show {
            opacity: 1;
            transform: translateX(-50%) translateY(0);
        }

        @media (max-width: 480px) {
            #toast { 
                white-space: normal; 
                text-align: center; 
                width: calc(100% - 48px);
                top: 10px;
            }
        }
    </style>
</head>
<body>

<nav>
    <a href="{{ url('/') }}">
        <img src="{{ asset('img/core-img/logo.png') }}" alt="Orderer" />
    </a>
    <a href="mailto:us@ordererweb.com">Get in touch</a>
</nav>

<main>
    <div class="container">

        <div class="eyebrow"><span></span> Coming soon</div>

        <h1>We are building<br>something <em>great.</em></h1>

        <p class="africgem">A product of <strong>AfricGEM International Company Limited</strong></p>

        <p class="description">
            Orderer is an all-in-one marketplace where you can <strong>buy, sell, and deliver anything</strong> — from everyday essentials and electronics to real estate, services, and last-mile rider bookings. We are putting the finishing touches on something built differently.
            <br /><br />
            Drop your email below and you will be the first to know when we go live — early access, launch deals, and everything in between.
        </p>

        <div class="form-wrap">
            <p>Get early access — no spam, just the good stuff.</p>

            <div class="err-box" id="err-box"></div>

            <form id="waitlist-form" action="{{ route('newsletter.subscribe') }}" method="POST">
                @csrf
                <div class="input-row">
                    <input
                        type="email"
                        name="email"
                        id="waitlist-email"
                        placeholder="you@example.com"
                        autocomplete="email"
                        required
                    />
                    <button type="submit" id="waitlist-btn">
                        <span id="btn-label">Notify me</span>
                        <span class="spinner" id="btn-spinner"></span>
                    </button>
                </div>
            </form>
        </div>

        <div class="pills">
            <span class="pill"><span class="pill-dot"></span> Multi-vendor marketplace</span>
            <span class="pill"><span class="pill-dot"></span> Rider &amp; delivery booking</span>
            <span class="pill"><span class="pill-dot"></span> Real estate listings</span>
            <span class="pill"><span class="pill-dot"></span> Services marketplace</span>
            <span class="pill"><span class="pill-dot"></span> Nigerian &amp; global brands</span>
            <span class="pill"><span class="pill-dot"></span> Escrow payment protection</span>
        </div>

        <p class="footer-note">
            By joining the waitlist you agree to receive email updates from Orderer.<br />
            Read our <a href="{{ url('/legal/privacy-policy') }}">Privacy Policy</a> and <a href="{{ url('/legal/terms-and-conditions') }}">Terms of Use</a>.
        </p>

    </div>
</main>

<footer>
    <p>&copy; {{ date('Y') }} Orderer. All rights reserved.</p>
    <p>A product of <strong>AfricGEM International Company Limited</strong></p>
</footer>

<div id="toast">
    <svg width="16" height="16" fill="none" viewBox="0 0 24 24" style="flex-shrink:0">
        <circle cx="12" cy="12" r="10" fill="#2ECC71"/>
        <path d="M7 12.5l3.5 3.5L17 9" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
    </svg>
    <span id="toast-msg"></span>
</div>

<script>
document.getElementById('waitlist-form').addEventListener('submit', async function (e) {
    e.preventDefault();

    const btn     = document.getElementById('waitlist-btn');
    const label   = document.getElementById('btn-label');
    const spinner = document.getElementById('btn-spinner');
    const errBox  = document.getElementById('err-box');
    const email   = document.getElementById('waitlist-email').value.trim();

    errBox.style.display  = 'none';
    errBox.textContent    = '';
    label.style.display   = 'none';
    spinner.style.display = 'block';
    btn.disabled          = true;

    try {
        const res = await fetch(this.action, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                'Accept': 'application/json',
            },
            body: JSON.stringify({ email }),
        });

        const data = await res.json();

        if (res.ok) {
            document.getElementById('waitlist-email').value = '';
            showToast(data.message || "You're on the list!");
        } else {
            const msg = data.errors?.email?.[0] || data.message || 'Something went wrong. Try again.';
            errBox.textContent   = msg;
            errBox.style.display = 'block';
        }
    } catch (err) {
        errBox.textContent   = 'Network error. Please check your connection and try again.';
        errBox.style.display = 'block';
    } finally {
        label.style.display   = 'inline';
        spinner.style.display = 'none';
        btn.disabled          = false;
    }
});

function showToast(msg) {
    const toast = document.getElementById('toast');
    document.getElementById('toast-msg').textContent = msg;
    toast.classList.add('show');
    setTimeout(() => toast.classList.remove('show'), 4500);
}
</script>

</body>
</html>