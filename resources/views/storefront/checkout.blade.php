@auth('web')
    @include('layouts.storefront.header-auth')
@else
    @include('layouts.storefront.header-guest')
@endauth
@include('layouts.storefront.cart-sidebar')
@include('layouts.partials.alerts')

<div class="breadcumb_area bg-img" style="background-image: url({{ asset('img/bg-img/breadcumb.jpeg') }});">
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
                            <select class="form-control w-100" name="shipping_country" id="countrySelect">
                                <option value="">Loading countries...</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label>Order Notes (optional)</label>
                            <textarea name="notes" class="form-control" rows="3"
                                      placeholder="Any special instructions..."></textarea>
                        </div>

                    </div>
                    {{-- ── Shipping Method ─────────────────────────────── --}}
                    <div class="checkout_details_area mt-30 clearfix" id="shippingSection" style="display:none;">
                        <div class="cart-page-heading mb-20">
                            <h5>Shipping Method</h5>
                            <p style="font-size:13px;color:#888;">
                                Select how you want your order delivered
                            </p>
                        </div>

                        {{-- Loading state --}}
                        <div id="ratesLoading" style="text-align:center;padding:20px;display:none;">
                            <div class="spinner-border text-success" role="status">
                                <span class="sr-only">Loading rates...</span>
                            </div>
                            <p style="margin-top:10px;color:#888;font-size:13px;">Fetching shipping rates...</p>
                        </div>

                        {{-- Rates list --}}
                        <div id="ratesList"></div>

                        {{-- Hidden fields populated by JS --}}
                        <input type="hidden" name="shipping_service_code" id="selectedServiceCode">
                        <input type="hidden" name="shipping_carrier"      id="selectedCarrier">
                        <input type="hidden" name="shipping_service_name" id="selectedServiceName">
                        <input type="hidden" name="shipping_fee"          id="selectedShippingFee" value="0">
                        <input type="hidden" name="package_weight"        id="packageWeight">
                        <input type="hidden" name="shipping_rate_data"    id="shippingRateData">
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
                        <li>
                            <span>Subtotal</span>
                            <span id="displaySubtotal"
                                  data-value="{{ $subtotal }}">
                                ${{ number_format($subtotal, 2) }}
                            </span>
                        </li>
                        <li>
                            <span>Shipping</span>
                            <span id="displayShippingFee" style="color:#2ECC71;">
                                Select shipping
                            </span>
                        </li>
                        <li style="font-weight:800;">
                            <span>Total</span>
                            <span id="displayTotal">${{ number_format($subtotal, 2) }}</span>
                        </li>
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
    (async function () {
        const select = document.getElementById('countrySelect');

        try {
            const res = await fetch('https://restcountries.com/v3.1/all?fields=name,cca2');
            const countries = await res.json();

            countries.sort((a, b) => a.name.common.localeCompare(b.name.common));

            select.innerHTML = '<option value="">-- Select Country --</option>';

            countries.forEach(country => {
                const option = document.createElement('option');
                option.value = country.cca2;
                option.textContent = country.name.common;
                if (country.cca2 === 'NG') option.selected = true;
                select.appendChild(option);
            });

        } catch (err) {
            console.error('Failed to load countries:', err);
            select.innerHTML = `
                <option value="">-- Select Country --</option>
                <option value="NG" selected>Nigeria</option>
                <option value="GH">Ghana</option>
                <option value="KE">Kenya</option>
                <option value="ZA">South Africa</option>
                <option value="US">United States</option>
                <option value="GB">United Kingdom</option>
            `;
        }
    })();
</script>

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

<script>
const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

// Fields that trigger rate fetch when all filled
const addressFields = [
    'shipping_name', 'shipping_phone', 'shipping_address',
    'shipping_city', 'shipping_state', 'shipping_country'
];
let rateFetchTimer = null;

addressFields.forEach(function(fieldName) {
    const el = document.querySelector('[name="' + fieldName + '"]');
    if (el) {
        el.addEventListener('input', scheduleRateFetch);
        el.addEventListener('change', scheduleRateFetch);
    }
});

function scheduleRateFetch() {
    clearTimeout(rateFetchTimer);
    rateFetchTimer = setTimeout(tryFetchRates, 800);
}

function allAddressFilled() {
    return addressFields.every(function(f) {
        const el = document.querySelector('[name="' + f + '"]');
        return el && el.value.trim().length > 0;
    });
}

function tryFetchRates() {
    if (!allAddressFilled()) return;

    const shippingSection = document.getElementById('shippingSection');
    const ratesLoading    = document.getElementById('ratesLoading');
    const ratesList       = document.getElementById('ratesList');

    shippingSection.style.display = 'block';
    ratesLoading.style.display    = 'block';
    ratesList.innerHTML           = '';

    // Reset hidden fields and order summary while loading
    document.getElementById('selectedServiceCode').value = '';
    document.getElementById('selectedShippingFee').value = '0';
    document.getElementById('displayShippingFee').textContent = 'Calculating...';

    const formData = {
        shipping_name:    document.querySelector('[name="shipping_name"]').value,
        shipping_phone:   document.querySelector('[name="shipping_phone"]').value,
        shipping_address: document.querySelector('[name="shipping_address"]').value,
        shipping_city:    document.querySelector('[name="shipping_city"]').value,
        shipping_state:   document.querySelector('[name="shipping_state"]').value,
        shipping_country: document.querySelector('[name="shipping_country"]').value,
    };

    fetch('{{ route("checkout.rates") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify(formData),
    })
    .then(r => r.json())
    .then(data => {
        ratesLoading.style.display = 'none';

        console.log('Shipbubble checkout rates:', data);

        if (!data.success || !data.rates || data.rates.length === 0) {
            ratesList.innerHTML = '<div class="alert alert-warning">No shipping rates available for this address. Please check the delivery details.</div>';
            document.getElementById('displayShippingFee').textContent = 'Not available';
            return;
        }

        let html = '';
        data.rates.forEach(function(rate, idx) {
            const courierName = rate.courier_name  || 'Courier';
            const serviceName = rate.service_type  || 'Standard';
            const price       = parseFloat(rate.total || 0).toFixed(2);
            const eta         = rate.delivery_eta  || '';
            const serviceCode = rate.service_code  || '';
            const logoUrl     = rate.courier_image || '';

            html += `
            <label class="d-block border rounded p-3 mb-2 rate-card"
                   for="rate_${idx}"
                   style="cursor:pointer;transition:all .2s;">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-3">
                        <input type="radio"
                               name="_shipping_rate_radio"
                               id="rate_${idx}"
                               value="${serviceCode}"
                               data-price="${price}"
                               data-carrier="${courierName}"
                               data-service="${serviceName}"
                               data-ratedata='${JSON.stringify(rate)}'
                               onchange="selectRate(this)"
                               class="form-check-input mt-0"
                               ${idx === 0 ? 'checked' : ''}>
                        <div class="d-flex align-items-center gap-2">
                            ${logoUrl ? `<img src="${logoUrl}" alt="${courierName}" style="height:28px;object-fit:contain;">` : ''}
                            <div>
                                <p class="mb-0 fw-bold" style="font-size:14px;">${courierName}</p>
                                <p class="mb-0 text-muted" style="font-size:12px;">${serviceName}</p>
                                ${eta ? `<small class="text-muted"><i class="fa fa-clock-o mr-1"></i>${eta}</small>` : ''}
                            </div>
                        </div>
                    </div>
                    <span style="font-size:18px;font-weight:800;color:#2ECC71;">$${price}</span>
                </div>
            </label>`;
        });

        ratesList.innerHTML = html;

        // Auto-select first rate
        const firstRadio = document.querySelector('[name="_shipping_rate_radio"]');
        if (firstRadio) selectRate(firstRadio);
    })
    .catch(function(err) {
        console.error('Rates fetch error:', err);
        ratesLoading.style.display = 'none';
        ratesList.innerHTML = '<div class="alert alert-danger">Could not fetch shipping rates. Please try again.</div>';
        document.getElementById('displayShippingFee').textContent = 'Error';
    });
}

function selectRate(radio) {
    // Populate hidden fields for form submission
    document.getElementById('selectedServiceCode').value = radio.value;
    document.getElementById('selectedCarrier').value     = radio.dataset.carrier;
    document.getElementById('selectedServiceName').value = radio.dataset.service;
    document.getElementById('selectedShippingFee').value = radio.dataset.price;
    document.getElementById('shippingRateData').value    = radio.dataset.ratedata;

    // Update order summary
    const shippingFee = parseFloat(radio.dataset.price);
    const subtotal    = parseFloat(document.getElementById('displaySubtotal').dataset.value || 0);

    document.getElementById('displayShippingFee').textContent = '$' + shippingFee.toFixed(2);
    document.getElementById('displayTotal').textContent       = '$' + (subtotal + shippingFee).toFixed(2);

    // Highlight selected card
    document.querySelectorAll('.rate-card').forEach(function(card) {
        card.style.borderColor = '#dee2e6';
        card.style.background  = '#fff';
    });
    radio.closest('.rate-card').style.borderColor = '#2ECC71';
    radio.closest('.rate-card').style.background  = '#f0faf5';
}

// Block form submission if no shipping selected
document.getElementById('checkoutForm').addEventListener('submit', function(e) {
    const serviceCode = document.getElementById('selectedServiceCode').value;
    if (!serviceCode) {
        e.preventDefault();
        alert('Please wait for shipping rates to load and select a shipping method.');
        return;
    }

    const fee = parseFloat(document.getElementById('selectedShippingFee').value);
    if (!fee || fee <= 0) {
        e.preventDefault();
        alert('Invalid shipping fee. Please select a shipping method.');
    }
});
</script>
</body>
</html>
