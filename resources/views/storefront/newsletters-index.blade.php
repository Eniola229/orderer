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
                    <h2>Blog</h2>
                    <p style="color:rgba(255,255,255,0.8);font-size:15px;margin-top:8px;">
                        Updates, tips, and news from Orderer
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<section class="section-padding-80" style="background:#f9fafb;">
    <div class="container">

    {{-- Section header --}}
    <div class="row mb-40">
        <div class="col-12 text-center">
            <p style="font-size:13px;font-weight:700;letter-spacing:2px;text-transform:uppercase;color:#2ECC71;margin-bottom:8px;">
                Stay in the loop
            </p>
            <h2 style="font-size:32px;font-weight:800;color:#1a1a2e;margin-bottom:12px;">
                Orderer Blog
            </h2>
            <p style="color:#888;font-size:15px;max-width:520px;margin:0 auto;">
                Browse all our posts — tips for sellers, buying guides, platform updates and more.
            </p>
        </div>
    </div>

        {{-- Newsletter grid --}}
        @if($newsletters->count())
        <div class="row">
            @foreach($newsletters as $nl)
            <div class="col-12 col-md-6 col-lg-4 mb-4">
                <a href="{{ route('newsletters.show', $nl->id) }}"
                   style="display:block;text-decoration:none;height:100%;">
                    <div class="nl-card" style="
                        background:#fff;
                        border-radius:14px;
                        overflow:hidden;
                        height:100%;
                        border:1px solid #eef0f2;
                        transition:transform .2s, box-shadow .2s;
                        display:flex;
                        flex-direction:column;
                    ">
                        {{-- Card header accent strip --}}
                        <div style="height:5px;background:linear-gradient(90deg,#2ECC71,#27ae60);"></div>

                        <div style="padding:24px 22px;flex:1;display:flex;flex-direction:column;">
                            {{-- Audience badge --}}
                            <div style="margin-bottom:14px;">
                                @if($nl->audience === 'sellers')
                                    <span class="nl-badge" style="background:#EAF6FF;color:#2980B9;">For Sellers</span>
                                @elseif($nl->audience === 'buyers')
                                    <span class="nl-badge" style="background:#FEF9E7;color:#B7770D;">For Buyers</span>
                                @else
                                    <span class="nl-badge" style="background:#EAFAF1;color:#1E8449;">Everyone</span>
                                @endif
                            </div>

                            {{-- Subject / title --}}
                            <h5 style="font-size:17px;font-weight:800;color:#1a1a2e;line-height:1.4;margin-bottom:10px;flex:1;">
                                {{ $nl->subject }}
                            </h5>

                            {{-- Preview snippet (strip HTML tags) --}}
                            <p style="font-size:13px;color:#888;line-height:1.7;margin-bottom:16px;">
                                {{ Str::limit(strip_tags($nl->body), 120) }}
                            </p>

                            {{-- Footer meta --}}
                            <div style="display:flex;align-items:center;justify-content:space-between;border-top:1px solid #f0f0f0;padding-top:14px;margin-top:auto;">
                                <span style="font-size:12px;color:#aaa;">
                                    <i class="fa fa-calendar-o" style="margin-right:4px;"></i>
                                    {{ $nl->sent_at ? $nl->sent_at->format('d M Y') : $nl->created_at->format('d M Y') }}
                                </span>
                                <span style="font-size:12px;font-weight:700;color:#2ECC71;">
                                    Read &rarr;
                                </span>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        @if($newsletters->hasPages())
        <nav aria-label="navigation">
            <ul class="pagination mt-50 mb-30">
                <li class="page-item {{ $newsletters->onFirstPage() ? 'disabled' : '' }}">
                    <a class="page-link" href="{{ $newsletters->previousPageUrl() }}">
                        <i class="fa fa-angle-left"></i>
                    </a>
                </li>
                @foreach($newsletters->getUrlRange(1, $newsletters->lastPage()) as $page => $url)
                <li class="page-item {{ $newsletters->currentPage() === $page ? 'active' : '' }}">
                    <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                </li>
                @endforeach
                <li class="page-item {{ !$newsletters->hasMorePages() ? 'disabled' : '' }}">
                    <a class="page-link" href="{{ $newsletters->nextPageUrl() }}">
                        <i class="fa fa-angle-right"></i>
                    </a>
                </li>
            </ul>
        </nav>
        @endif

        @else
        {{-- Empty state --}}
        <div class="col-12 text-center py-5">
            <i class="fa fa-envelope-o" style="font-size:56px;color:#ddd;"></i>
            <h5 style="margin-top:20px;color:#aaa;font-weight:600;">No posts published yet.</h5>
            <p style="color:#bbb;font-size:14px;">Check back soon — we publish updates regularly.</p>
        </div>
        @endif

    </div>
</section>

@include('layouts.storefront.footer')

<style>
.nl-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 32px rgba(0,0,0,0.09);
}
.nl-badge {
    display: inline-block;
    font-size: 11px;
    font-weight: 700;
    padding: 3px 10px;
    border-radius: 20px;
    letter-spacing: .4px;
}
.section-padding-80 {
    padding: 80px 0;
}
.mb-40 { margin-bottom: 40px; }
.mt-50 { margin-top: 50px; }
.mb-30 { margin-bottom: 30px; }
</style>

<script src="{{ asset('js/jquery/jquery-2.2.4.min.js') }}"></script>
<script src="{{ asset('js/popper.min.js') }}"></script>
<script src="{{ asset('js/bootstrap.min.js') }}"></script>
<script src="{{ asset('js/plugins.js') }}"></script>
<script src="{{ asset('js/classy-nav.min.js') }}"></script>
<script src="{{ asset('js/active.js') }}"></script>
</body>
</html>