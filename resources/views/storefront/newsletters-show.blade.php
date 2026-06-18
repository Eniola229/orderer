@auth('web')
    @include('layouts.storefront.header-auth')
@else
    @include('layouts.storefront.header-guest')
@endauth

@include('layouts.storefront.cart-sidebar')
@include('layouts.partials.alerts')

{{-- Breadcrumb --}}
<div class="breadcumb_area bg-img" style="background-image: url({{ asset('img/bg-img/breadcumb.jpeg') }});">
    <div class="container h-100">
        <div class="row h-100 align-items-center">
            <div class="col-12">
                <div class="page-title text-center">
                    <h2 style="font-size:clamp(18px,4vw,32px);">{{ Str::limit($newsletter->subject, 60) }}</h2>
                    <p style="color:rgba(255,255,255,0.75);font-size:14px;margin-top:8px;">
                        <a href="{{ route('newsletters.index') }}"
                           style="color:rgba(255,255,255,0.75);text-decoration:none;">
                           <i class="fa fa-arrow-left" style="margin-right:6px;"></i>Back to Blog
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<section class="section-padding-80" style="background:#f9fafb;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-8">

                {{-- Newsletter card --}}
                <div style="
                    background:#fff;
                    border-radius:16px;
                    overflow:hidden;
                    border:1px solid #eef0f2;
                    box-shadow:0 4px 24px rgba(0,0,0,0.06);
                ">
                    {{-- Top accent --}}
                    <div style="height:5px;background:linear-gradient(90deg,#2ECC71,#27ae60);"></div>

                    {{-- Header meta --}}
                    <div style="padding:28px 32px 20px;border-bottom:1px solid #f0f0f0;">
                        {{-- Audience badge --}}
                        <div style="margin-bottom:12px;">
                            @if($newsletter->audience === 'sellers')
                                <span style="display:inline-block;background:#EAF6FF;color:#2980B9;font-size:11px;font-weight:700;padding:3px 12px;border-radius:20px;">
                                    For Sellers
                                </span>
                            @elseif($newsletter->audience === 'buyers')
                                <span style="display:inline-block;background:#FEF9E7;color:#B7770D;font-size:11px;font-weight:700;padding:3px 12px;border-radius:20px;">
                                    For Buyers
                                </span>
                            @else
                                <span style="display:inline-block;background:#EAFAF1;color:#1E8449;font-size:11px;font-weight:700;padding:3px 12px;border-radius:20px;">
                                    Everyone
                                </span>
                            @endif
                        </div>

                        <h1 style="font-size:clamp(20px,3vw,28px);font-weight:800;color:#1a1a2e;line-height:1.35;margin-bottom:14px;">
                            {{ $newsletter->subject }}
                        </h1>

                        <div style="display:flex;align-items:center;gap:18px;flex-wrap:wrap;">
                            {{-- Orderer logo / sender --}}
                            <div style="display:flex;align-items:center;gap:10px;">
                                <div style="width:36px;height:36px;border-radius:50%;background:#2ECC71;display:flex;align-items:center;justify-content:center;font-weight:800;font-size:14px;color:#fff;flex-shrink:0;">
                                    O
                                </div>
                                <div>
                                    <p style="margin:0;font-size:13px;font-weight:700;color:#1a1a2e;">The Orderer Team</p>
                                    <p style="margin:0;font-size:11px;color:#aaa;">ordererweb.com</p>
                                </div>
                            </div>

                            <span style="color:#ddd;">|</span>

                            <span style="font-size:13px;color:#aaa;">
                                <i class="fa fa-calendar-o" style="margin-right:5px;"></i>
                                {{ $newsletter->sent_at ? $newsletter->sent_at->format('d F Y') : $newsletter->created_at->format('d F Y') }}
                            </span>

                            {{-- Share button --}}
                            <button onclick="shareNewsletter()" style="
                                margin-left:auto;
                                background:none;
                                border:1px solid #2ECC71;
                                border-radius:50px;
                                padding:5px 16px;
                                font-size:12px;
                                color:#2ECC71;
                                cursor:pointer;
                                font-weight:600;
                            ">
                                <i class="fa fa-share-alt" style="margin-right:4px;"></i> Share
                            </button>
                        </div>
                    </div>

                    {{-- Newsletter body --}}
                    <div class="nl-body" style="padding:32px;">
                        {!! $newsletter->body !!}
                    </div>

                    {{-- Footer CTA --}}
                    <div style="
                        background:#f8fffe;
                        border-top:1px solid #eef0f2;
                        padding:24px 32px;
                        text-align:center;
                    ">
                        <p style="font-size:14px;color:#888;margin-bottom:14px;">
                            Want to receive updates like this directly in your inbox?
                        </p>
                        <div style="display:flex;gap:10px;max-width:380px;margin:0 auto;flex-wrap:wrap;justify-content:center;">
                            <input type="email" id="subscribeEmail" placeholder="Enter your email"
                                   style="flex:1;min-width:200px;border:1.5px solid #e0e0e0;border-radius:6px;padding:10px 14px;font-size:14px;outline:none;"
                                   required>
                            <button type="button" onclick="subscribeNewsletter(this)"
                                    class="btn essence-btn" style="white-space:nowrap;">
                                Subscribe
                            </button>
                        </div>
                        <p style="font-size:11px;color:#bbb;margin-top:10px;margin-bottom:0;">
                            No spam, ever. Unsubscribe anytime.
                        </p>
                    </div>
                </div>

                {{-- Navigation: prev / back / next --}}
                <div style="display:flex;align-items:center;justify-content:space-between;margin-top:32px;flex-wrap:wrap;gap:12px;">
                    <a href="{{ route('newsletters.index') }}"
                       style="display:inline-flex;align-items:center;gap:8px;font-size:14px;font-weight:600;color:#2ECC71;text-decoration:none;">
                        <i class="fa fa-th-large"></i> All Posts
                    </a>
                    <a href="{{ route('shop.index') }}"
                       class="btn essence-btn"
                       style="font-size:13px;padding:10px 22px;">
                        Shop Now <i class="fa fa-arrow-right ml-1"></i>
                    </a>
                </div>

            </div>
        </div>
    </div>
</section>

@include('layouts.storefront.footer')

<style>
.section-padding-80 { padding: 80px 0; }

/* Newsletter body styles — renders the HTML email content cleanly */
.nl-body {
    font-size: 15px;
    color: #444;
    line-height: 1.85;
    word-break: break-word;
}
.nl-body h1, .nl-body h2, .nl-body h3 {
    color: #1a1a2e;
    font-weight: 800;
    margin-top: 28px;
    margin-bottom: 12px;
}
.nl-body h1 { font-size: 24px; }
.nl-body h2 { font-size: 20px; }
.nl-body h3 { font-size: 17px; }
.nl-body p  { margin-bottom: 16px; }
.nl-body a  { color: #2ECC71; font-weight: 600; }
.nl-body img {
    max-width: 100%;
    height: auto;
    border-radius: 8px;
    margin: 12px 0;
}
.nl-body ul, .nl-body ol {
    padding-left: 22px;
    margin-bottom: 16px;
}
.nl-body li { margin-bottom: 6px; }
.nl-body blockquote {
    border-left: 4px solid #2ECC71;
    padding: 12px 20px;
    background: #f4fdf8;
    border-radius: 0 8px 8px 0;
    color: #555;
    margin: 20px 0;
    font-style: italic;
}
.nl-body table {
    width: 100%;
    border-collapse: collapse;
    margin: 16px 0;
    font-size: 14px;
}
.nl-body table td, .nl-body table th {
    padding: 10px 14px;
    border: 1px solid #eee;
}
.nl-body table th {
    background: #f4fdf8;
    font-weight: 700;
    color: #1a1a2e;
}

@media (max-width: 576px) {
    .nl-body { padding: 20px !important; font-size: 14px; }
    .nl-body h1 { font-size: 20px; }
    .nl-body h2 { font-size: 17px; }
}
</style>

<script src="{{ asset('js/jquery/jquery-2.2.4.min.js') }}"></script>
<script src="{{ asset('js/popper.min.js') }}"></script>
<script src="{{ asset('js/bootstrap.min.js') }}"></script>
<script src="{{ asset('js/plugins.js') }}"></script>
<script src="{{ asset('js/classy-nav.min.js') }}"></script>
<script src="{{ asset('js/active.js') }}"></script>
<script>
function subscribeNewsletter(btn) {
    const email = document.getElementById('subscribeEmail').value.trim();

    if (!email || !email.includes('@')) {
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'warning',
            title: 'Please enter a valid email address.',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
        });
        return;
    }

    // Loading state
    const original = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Subscribing…';

    fetch('{{ route("newsletter.subscribe") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        },
        body: JSON.stringify({ email }),
    })
    .then(r => r.json())
    .then(data => {
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: data.success ? 'success' : 'error',
            title: data.message ?? (data.success ? 'Subscribed!' : 'Something went wrong.'),
            showConfirmButton: false,
            timer: 4000,
            timerProgressBar: true,
        });

        if (data.success) {
            document.getElementById('subscribeEmail').value = '';
        }
    })
    .catch(() => {
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'error',
            title: 'Something went wrong. Please try again.',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
        });
    })
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = original;
    });
}

function shareNewsletter() {
    const url  = window.location.href;
    const title = @json($newsletter->subject);

    if (navigator.share) {
        navigator.share({ title, url }).catch(() => {});
    } else {
        navigator.clipboard.writeText(url)
            .then(() => {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: 'Link copied to clipboard!',
                    showConfirmButton: false,
                    timer: 2000,
                    timerProgressBar: true,
                });
            });
    }
}
</script>
</body>
</html>