@auth('web')@include('layouts.storefront.header-auth')@else @include('layouts.storefront.header-guest')@endauth
@include('layouts.storefront.cart-sidebar')
@include('layouts.partials.alerts')

<div class="breadcumb_area bg-img" style="background-image:url({{ asset('img/bg-img/breadcumb.jpg') }});">
    <div class="container h-100"><div class="row h-100 align-items-center"><div class="col-12">
        <div class="page-title text-center"><h2>Services</h2></div>
    </div></div></div>
</div>

<section class="section-padding-80">
    <div class="container">
        <div class="row">
            @forelse($services as $service)
            <div class="col-12 col-md-6 col-lg-4 mb-4">
                <div style="border:1px solid #eee;border-radius:12px;overflow:hidden;height:100%;">
                    @if($service->portfolio_images && count($service->portfolio_images))
                    <img src="{{ $service->portfolio_images[0]['url'] }}"
                         style="width:100%;height:180px;object-fit:cover;" alt="">
                    @else
                    <div style="width:100%;height:180px;background:#f0faf5;display:flex;align-items:center;justify-content:center;">
                        <i class="fa fa-cogs" style="font-size:48px;color:#2ECC71;opacity:.4;"></i>
                    </div>
                    @endif
                    <div style="padding:20px;">
                        <span style="font-size:12px;color:#2ECC71;font-weight:600;">{{ $service->category->name??'' }}</span>
                        <h6 style="font-weight:700;margin:6px 0;">{{ Str::limit($service->title, 50) }}</h6>
                        <p style="color:#888;font-size:13px;margin-bottom:12px;">{{ Str::limit($service->description, 100) }}</p>
                        <div style="display:flex;align-items:center;justify-content:space-between;">
                            <div>
                                @if($service->pricing_type === 'negotiable')
                                    <span style="font-weight:700;color:#1a1a1a;">Negotiable</span>
                                @else
                                    <span style="font-weight:700;color:#2ECC71;font-size:18px;">
                                        ${{ number_format($service->price,2) }}
                                    </span>
                                    <small style="color:#888;">{{ $service->pricing_type==='hourly'?'/hr':'' }}</small>
                                @endif
                            </div>
                            @if($service->delivery_time)
                            <small style="color:#888;"><i class="fa fa-clock-o mr-1"></i>{{ $service->delivery_time }}</small>
                            @endif
                        </div>
                        <div style="margin-top:12px;padding-top:12px;border-top:1px solid #f5f5f5;display:flex;align-items:center;gap:8px;">
                            @if($service->seller->avatar)
                                <img src="{{ $service->seller->avatar }}" style="width:28px;height:28px;border-radius:50%;object-fit:cover;" alt="">
                            @else
                                <div style="width:28px;height:28px;border-radius:50%;background:#2ECC71;color:#fff;display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:700;">
                                    {{ strtoupper(substr($service->seller->business_name??'S',0,1)) }}
                                </div>
                            @endif
                            <small style="color:#666;font-weight:600;">{{ $service->seller->business_name??'Seller' }}</small>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12 text-center py-5 text-muted">
                <i class="fa fa-cogs" style="font-size:48px;color:#ddd;margin-bottom:16px;display:block;"></i>
                <p>No services available yet.</p>
            </div>
            @endforelse
        </div>
        <div>{{ $services->links() }}</div>
    </div>
</section>

@include('layouts.storefront.footer')
<script src="{{ asset('js/jquery/jquery-2.2.4.min.js') }}"></script>
<script src="{{ asset('js/bootstrap.min.js') }}"></script>
<script src="{{ asset('js/classy-nav.min.js') }}"></script>
<script src="{{ asset('js/active.js') }}"></script>
</body>
</html>
