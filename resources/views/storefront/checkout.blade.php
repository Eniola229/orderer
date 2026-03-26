<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Checkout — Orderer</title>
    <link rel="icon" href="{{ asset('img/core-img/favicon.ico') }}">
    <link rel="stylesheet" href="{{ asset('css/core-style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/orderer.css') }}">
</head>
<body>

@auth('web')
    @include('layouts.storefront.header-auth')
@else
    @include('layouts.storefront.header-guest')
@endauth
@include('layouts.storefront.cart-sidebar')
@include('layouts.partials.alerts')

<div class="breadcumb_area bg-img" style="background-image: url({{ asset('img/bg-img/breadcumb.jpg') }});">
    <div class="container h-100">
        <div class="row h-100 align-items-center">
            <div class="col-12">
                <div class="page-title text-center"><h2>Checkout</h2></div>
            </div>
        </div>
    </div>
</div>

<div class="checkout_area section-padding-80">
    <div class="container">
        @if(empty($cartItems))
        <div class="text-center py-5">
            <i class="fa fa-shopping-bag" style="font-size:60px;color:#ddd;"></i>
            <h4 class="mt-3">Your cart is empty</h4>
            <a href="{{ route('shop.index') }}" class="btn essence-btn mt-3">Browse Shop</a>
        </div>
        @else
        <form action="{{ route('checkout.place') }}" method="POST" id="checkoutForm">
            @csrf
            <div class="row">

                {{-- Left: shipping details --}}
                <div class="col-12 col-md-6">
                    <div class="checkout_details_area mt-50 clearfix">

                        <div class="cart-page-heading mb-30">
                            <h5>Delivery Address</h5>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="shipping_name">Full Name <span style="color:red">*</span></label>
                                <input type="text" class="form-control @error('shipping_name') is-invalid @enderror"
                                       id="shipping_name" name="shipping_name"
                                       value="{{ old('shipping_name', auth('web')->user()->full_name ?? '') }}" required>
                                @error('shipping_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="shipping_phone">Phone <span style="color:red">*</span></label>
                                <input type="tel" class="form-control @error('shipping_phone') is-invalid @enderror"
                                       id="shipping_phone" name="shipping_phone"
                                       value="{{ old('shipping_phone', auth('web')->user()->phone ?? '') }}" required>
                                @error('shipping_phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="shipping_address">Street Address <span style="color:red">*</span></label>
                            <input type="text" class="form-control @error('shipping_address') is-invalid @enderror"
                                   id="shipping_address" name="shipping_address"
                                   value="{{ old('shipping_address') }}" placeholder="House number, street name" required>
                            @error('shipping_address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label>City <span style="color:red">*</span></label>
                                <input type="text" class="form-control" name="shipping_city"
                                       value="{{ old('shipping_city') }}" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label>State <span style="color:red">*</span></label>
                                <input type="text" class="form-control" name="shipping_state"
                                       value="{{ old('shipping_state') }}" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label>ZIP Code</label>
                                <input type="text" class="form-control" name="shipping_zip"
                                       value="{{ old('shipping_zip') }}">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label>Country <span style="color:red">*</span></label>
                            <select class="form-control w-100" name="shipping_country">
                                <option value="NG" selected>Nigeria</option>
                                <option value="GH">Ghana</option>
                                <option value="KE">Kenya</option>
                                <option value="ZA">South Africa</option>
                                <option value="US">United States</option>
                                <option value="GB">United Kingdom</option>
                                <option value="OTHER">Other</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label>Order Notes (optional)</label>
                            <textarea name="notes" class="form-control" rows="3"
                                      placeholder="Any special instructions..."></textarea>
                        </div>

                    </div>
                </div>

                {{-- Right: order summary + payment --}}
                <div class="col-12 col-md-6 col-lg-5 ml-lg-auto">
                    <div class="order-details-confirmation mt-50">

                        <div class="cart-page-heading">
                            <h5>Your Order</h5>
                        </div>

                        <ul class="order-details-form mb-4">
                            <li><span>Product</span><span>Total</span></li>
                            @foreach($cartItems as $item)
                            <li>
                                <span>{{ Str::limit($item['name'], 30) }} × {{ $item['quantity'] }}</span>
                                <span>${{ number_format($item['price'] * $item['quantity'], 2) }}</span>
                            </li>
                            @endforeach
                            <li><span>Subtotal</span><span>${{ number_format($subtotal, 2) }}</span></li>
                            <li><span>Shipping</span><span>Calculated after</span></li>
                            <li style="font-weight:800;"><span>Total</span><span>${{ number_format($subtotal, 2) }}</span></li>
                        </ul>

                        {{-- Payment method --}}
                        <div id="accordion" role="tablist" class="mb-4">

                            {{-- Wallet --}}
                            <div class="card">
                                <div class="card-header" role="tab" id="headingWallet">
                                    <h6 class="mb-0">
                                        <a data-toggle="collapse" href="#collapseWallet"
                                           aria-expanded="{{ auth('web')->user()->wallet_balance >= $subtotal ? 'true' : 'false' }}">
                                            <i class="fa fa-circle-o mr-3"></i>
                                            Wallet Balance
                                            <span style="float:right;font-size:12px;color:#2ECC71;font-weight:700;">
                                                ${{ number_format(auth('web')->user()->wallet_balance, 2) }} available
                                            </span>
                                        </a>
                                    </h6>
                                </div>
                                <div id="collapseWallet"
                                     class="collapse {{ auth('web')->user()->wallet_balance >= $subtotal ? 'show' : '' }}"
                                     role="tabpanel" data-parent="#accordion">
                                    <div class="card-body">
                                        @if(auth('web')->user()->wallet_balance >= $subtotal)
                                        <div class="alert alert-success mb-0">
                                            <i class="fa fa-check-circle mr-2"></i>
                                            Your wallet balance covers this order. No extra payment needed.
                                        </div>
                                        <input type="hidden" name="payment_method" value="wallet">
                                        @else
                                        <div class="alert alert-warning mb-0">
                                            Insufficient balance (${{ number_format(auth('web')->user()->wallet_balance, 2) }}).
                                            <a href="{{ route('buyer.wallet') }}" style="color:#2ECC71;">Top up wallet</a> or pay with Korapay.
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            {{-- Korapay --}}
                            <div class="card">
                                <div class="card-header" role="tab" id="headingKorapay">
                                    <h6 class="mb-0">
                                        <a class="collapsed" data-toggle="collapse" href="#collapseKorapay"
                                           aria-expanded="false">
                                            <i class="fa fa-circle-o mr-3"></i>
                                            Pay with Korapay
                                        </a>
                                    </h6>
                                </div>
                                <div id="collapseKorapay" class="collapse" role="tabpanel"
                                     data-parent="#accordion">
                                    <div class="card-body">
                                        <p class="mb-0 text-muted" style="font-size:13px;">
                                            <i class="fa fa-lock mr-2" style="color:#2ECC71;"></i>
                                            Secure USD payment via Korapay. You'll be redirected to complete payment.
                                        </p>
                                        <input type="hidden" name="payment_method_korapay" value="korapay">
                                    </div>
                                </div>
                            </div>

                        </div>

                        {{-- Terms --}}
                        <div class="custom-control custom-checkbox d-block mb-4">
                            <input type="checkbox" class="custom-control-input" id="terms" required>
                            <label class="custom-control-label" for="terms">
                                I agree to the <a href="#" style="color:#2ECC71;">Terms &amp; Conditions</a>
                            </label>
                        </div>

                        <button type="submit" class="btn essence-btn w-100" style="font-size:16px;padding:14px;">
                            <i class="fa fa-shopping-bag mr-2"></i> Place Order
                        </button>

                    </div>
                </div>

            </div>
        </form>
        @endif
    </div>
</div>

@include('layouts.storefront.footer')

<script src="{{ asset('js/jquery/jquery-2.2.4.min.js') }}"></script>
<script src="{{ asset('js/popper.min.js') }}"></script>
<script src="{{ asset('js/bootstrap.min.js') }}"></script>
<script src="{{ asset('js/plugins.js') }}"></script>
<script src="{{ asset('js/classy-nav.min.js') }}"></script>
<script src="{{ asset('js/active.js') }}"></script>
<script>
// Set payment method based on accordion selection
document.querySelectorAll('[data-toggle="collapse"]').forEach(function(el) {
    el.addEventListener('click', function() {
        const target = this.getAttribute('href');
        if (target === '#collapseWallet') {
            document.querySelector('[name="payment_method"]') &&
            document.querySelector('[name="payment_method"]').setAttribute('name', 'payment_method');
        } else {
            document.querySelector('[name="payment_method_korapay"]') &&
            document.querySelector('[name="payment_method_korapay"]').setAttribute('name', 'payment_method');
        }
    });
});
</script>
</body>
</html>
