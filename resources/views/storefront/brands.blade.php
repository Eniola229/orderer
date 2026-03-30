@auth('web')@include('layouts.storefront.header-auth')@else @include('layouts.storefront.header-guest')@endauth
@include('layouts.storefront.cart-sidebar')
@include('layouts.partials.alerts')

<div class="breadcumb_area bg-img" style="background-image:url({{ asset('img/bg-img/breadcumb.jpeg') }});">
    <div class="container h-100"><div class="row h-100 align-items-center"><div class="col-12">
        <div class="page-title text-center"><h2>All Brands</h2></div>
    </div></div></div>
</div>

<section class="section-padding-80">
    <div class="container">

        {{-- Search Bar --}}
        <div class="row mb-4">
            <div class="col-12 col-md-6 mx-auto">
                <form action="{{ route('brands.index') }}" method="GET">
                    <div class="input-group" style="box-shadow:0 2px 10px rgba(0,0,0,0.08);border-radius:50px;">
                        <input type="text"
                               name="search"
                               class="form-control"
                               placeholder="Search brands..."
                               value="{{ request('search') }}"
                               style="border-radius:50px 0 0 50px;border-color:green;padding:12px 20px;">
                        <button class="btn essence-btn" type="submit">
                            <i class="fa fa-search mr-2"></i> Search
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Results count --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <p class="mb-0 text-muted" style="font-size:14px;">
                @if(request('search'))
                    Results for <strong>"{{ request('search') }}"</strong> —
                @endif
                <strong>{{ $brands->total() }}</strong> brand{{ $brands->total() !== 1 ? 's' : '' }} found
            </p>
            @if(request('search'))
            <a href="{{ route('brands.index') }}" class="btn btn-sm" style="background:#f5f5f5;border-radius:8px;font-size:13px;color:#666;">
                <i class="fa fa-times mr-1"></i> Clear
            </a>
            @endif
        </div>

        {{-- ── BANNER AD ──────────────────────────────────────────────
             Shown between search bar and brand cards.
             ────────────────────────────────────────────────────────── --}}
        @php
            $brandsBannerAd = \App\Helpers\AdHelper::forSlot('search_results', 1)->first();
            if ($brandsBannerAd) {
                \App\Helpers\AdHelper::recordImpression($brandsBannerAd->id, auth('web')->id());
            }
        @endphp

        @if($brandsBannerAd)
        <div class="row mb-4">
            <div class="col-12">
                <a href="{{ $brandsBannerAd->clickTrackingUrl() }}"
                   style="display:block;position:relative;border-radius:10px;overflow:hidden;">
                    @if($brandsBannerAd->media_type === 'image' && $brandsBannerAd->media_url)
                    <img src="{{ $brandsBannerAd->media_url }}"
                         alt="{{ $brandsBannerAd->title }}"
                         style="width:100%;max-height:130px;object-fit:cover;border-radius:10px;">
                    @else
                    <div style="background:linear-gradient(135deg,#1a1a2e,#2ECC71);border-radius:10px;padding:22px 28px;display:flex;align-items:center;justify-content:space-between;">
                        <span style="color:#fff;font-size:17px;font-weight:700;">{{ $brandsBannerAd->title }}</span>
                        <span style="background:#fff;color:#2ECC71;padding:7px 16px;border-radius:18px;font-size:13px;font-weight:700;">View</span>
                    </div>
                    @endif
                    <span style="position:absolute;top:8px;right:8px;background:rgba(0,0,0,.55);color:#fff;font-size:10px;padding:2px 8px;border-radius:8px;">
                        Sponsored
                    </span>
                </a>
            </div>
        </div>
        @endif
        {{-- END BANNER AD --}}


        {{-- Brand cards --}}
        <div class="row">
            @forelse($brands as $brand)
            <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-4">
                <a href="{{ route('brands.show', $brand->slug) }}" class="text-decoration-none">
                    <div class="brand-card" style="border:1px solid #eee;border-radius:14px;overflow:hidden;transition:all .25s;background:#fff;">

                        {{-- Card image / logo area --}}
                        <div style="background:#f8fdf9;padding:28px 20px;text-align:center;position:relative;border-bottom:1px solid #f0f0f0;">

                            {{-- Verified badge --}}
                            @if($brand->seller && $brand->seller->is_verified_business)
                            <span style="position:absolute;top:10px;left:10px;background:#2ECC71;color:#fff;padding:3px 9px;border-radius:20px;font-size:10px;font-weight:700;display:inline-flex;align-items:center;gap:3px;">
                                <i class="fa fa-check-circle" style="font-size:10px;"></i> Verified
                            </span>
                            @endif

                            @if($brand->logo)
                                <img src="{{ $brand->logo }}"
                                     style="height:70px;max-width:140px;object-fit:contain;" alt="{{ $brand->name }}">
                            @else
                                <div style="width:70px;height:70px;border-radius:50%;background:linear-gradient(135deg,#2ECC71,#27ae60);color:#fff;display:flex;align-items:center;justify-content:center;font-size:26px;font-weight:800;margin:0 auto;">
                                    {{ strtoupper(substr($brand->name, 0, 1)) }}
                                </div>
                            @endif
                        </div>

                        {{-- Card body --}}
                        <div style="padding:14px 16px;">
                            <h6 style="font-weight:700;color:#1a1a1a;margin-bottom:5px;font-size:15px;">
                                {{ $brand->name }}
                            </h6>

                            {{-- Star rating --}}
                            <div style="display:flex;align-items:center;gap:5px;margin-bottom:8px;">
                                <span style="color:#F39C12;font-size:12px;letter-spacing:1px;">
                                    @for($i=1;$i<=5;$i++){{ $i<=round($brand->average_rating)?'★':'☆' }}@endfor
                                </span>
                                <span style="color:#aaa;font-size:11px;">({{ $brand->total_reviews }})</span>
                            </div>

                            {{-- Seller name --}}
                            @if($brand->seller)
                            <p style="font-size:11px;color:#999;margin:0;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                                <i class="fa fa-store" style="color:#2ECC71;margin-right:3px;"></i>
                                {{ $brand->seller->business_name ?? '' }}
                            </p>
                            @endif
                        </div>

                    </div>
                </a>
            </div>
            @empty
            <div class="col-12 text-center py-5 text-muted">
                <i class="fa fa-tag" style="font-size:48px;color:#ddd;margin-bottom:16px;display:block;"></i>
                <p>{{ request('search') ? 'No brands found matching "' . request('search') . '".' : 'No brands yet.' }}</p>
                @if(request('search'))
                <a href="{{ route('brands.index') }}" class="btn essence-btn mt-2">Clear Search</a>
                @endif
            </div>
            @endforelse
        </div>

        <div class="mt-4">{{ $brands->appends(request()->query())->links() }}</div>

    </div>
</section>

@include('layouts.storefront.footer')
<script src="{{ asset('js/jquery/jquery-2.2.4.min.js') }}"></script>
<script src="{{ asset('js/bootstrap.min.js') }}"></script>
<script src="{{ asset('js/classy-nav.min.js') }}"></script>
<script src="{{ asset('js/active.js') }}"></script>

<style>
.brand-card:hover {
    border-color: #2ECC71 !important;
    box-shadow: 0 6px 24px rgba(46,204,113,0.13);
    transform: translateY(-4px);
}
</style>
</body>
</html>