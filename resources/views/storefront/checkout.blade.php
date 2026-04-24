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

<style> 
    .rate-card {
        cursor: pointer;
        transition: all 0.2s ease;
        border: 1px solid #dee2e6;
    }
    .rate-card:hover {
        border-color: #2ECC71 !important;
        background: #f0faf5;
    }
    .rate-card.selected {
        border-color: #2ECC71;
        background: #f0faf5;
    }
    .payment-method-card {
        cursor: pointer;
        transition: all 0.2s ease;
        border: 1px solid #dee2e6;
    }
    .payment-method-card:hover {
        border-color: #2ECC71;
    }
    .payment-method-card.selected {
        border-color: #2ECC71;
        background: #f0faf5;
    }
    .order-loader {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.9);
        z-index: 9999;
        display: none;
        align-items: center;
        justify-content: center;
        flex-direction: column;
    }
    .order-loader .spinner-border {
        width: 60px;
        height: 60px;
    }
    .error-message-box {
        background: #fee;
        border-left: 4px solid #dc3545;
        padding: 15px;
        margin-bottom: 15px;
        border-radius: 8px;
    }
    .required-field::after {
        content: "*";
        color: red;
        margin-left: 4px;
    }
</style>

<div class="checkout_area section-padding-80">
    <div class="container">
        @if(empty($cartItems))
        <div class="text-center py-5">
            <i class="fa fa-shopping-bag" style="font-size:60px;color:#ddd;"></i>
            <h4 class="mt-3">Your cart is empty</h4>
            <a href="{{ route('shop.index') }}" class="btn essence-btn mt-3">Browse Shop</a>
        </div>
        @else
        <form action="{{ isset($isBuyNow) && $isBuyNow ? route('buy-now.place') : route('checkout.place') }}" method="POST" id="checkoutForm">
            @csrf
            <div class="row">

                {{-- Left: shipping details --}}
                <div class="col-12 col-md-6">
                    <div class="checkout_details_area mt-50 clearfix">

                        <div class="cart-page-heading mb-30">
                            <h5>Delivery Address</h5>
                            <p class="text-muted small">Please fill all required fields to see shipping rates</p>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="required-field">Full Name</label>
                                <input type="text" class="form-control" id="shipping_name" name="shipping_name" value="{{ auth('web')->user()->full_name ?? '' }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="required-field">Phone</label>
                                <input type="tel" class="form-control" id="shipping_phone" name="shipping_phone" value="{{ auth('web')->user()->phone ?? '' }}" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="required-field">Full Address</label> <br>
                            <small>No, Street, city, state, country - We advice copying your address directly from google map, also  make sure to add country</small>
                            <input type="text" class="form-control" id="shipping_address" name="shipping_address" placeholder="House number, street name, city, state, country" required>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="required-field">City</label>
                                <input type="text" class="form-control" id="shipping_city" name="shipping_city" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="required-field">State</label>
                                <input type="text" class="form-control" id="shipping_state" name="shipping_state" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label>ZIP Code</label>
                                <input type="text" class="form-control" name="shipping_zip">
                            </div>
                        </div>

                    
                                <div class="row">
                                <div class="col-md-6 mb-3">
                                <label class="form-label required-field">Country</label><br>
                                <select name="shipping_country" id="countrySelect"
                                    class="form-control"
                                    required
                                    style="width: 100% !important; display: block; height: 48px; font-size: 15px;">
                                <option value="">-- Select Country --</option>
                                <option value="Nigeria" selected>Nigeria</option>
                                <option value="Afghanistan">Afghanistan</option>
                                <option value="Albania">Albania</option>
                                <option value="Algeria">Algeria</option>
                                <option value="United States">United States</option>
                                <option value="United Kingdom">United Kingdom</option>
                                <option value="Canada">Canada</option>
                                <option value="Australia">Australia</option>
                                <option value="Germany">Germany</option>
                                <option value="France">France</option>
                                <option value="Italy">Italy</option>
                                <option value="Spain">Spain</option>
                                <option value="China">China</option>
                                <option value="India">India</option>
                                <option value="South Africa">South Africa</option>
                                <option value="Ghana">Ghana</option>
                                <option value="Kenya">Kenya</option>
                                <option value="Egypt">Egypt</option>
                                <option value="Morocco">Morocco</option>
                                <option value="Brazil">Brazil</option>
                                <option value="Mexico">Mexico</option>
                                <option value="Japan">Japan</option>
                                <option value="South Korea">South Korea</option>
                                <option value="Russia">Russia</option>
                                <option value="Turkey">Turkey</option>
                                <option value="Saudi Arabia">Saudi Arabia</option>
                                <option value="UAE">United Arab Emirates</option>
                                <option value="Pakistan">Pakistan</option>
                                <option value="Bangladesh">Bangladesh</option>
                                <option value="Indonesia">Indonesia</option>
                                <option value="Malaysia">Malaysia</option>
                                <option value="Thailand">Thailand</option>
                                <option value="Vietnam">Vietnam</option>
                                <option value="Philippines">Philippines</option>
                                <option value="Sri Lanka">Sri Lanka</option>
                                <option value="Nepal">Nepal</option>
                                <option value="Israel">Israel</option>
                                <option value="Qatar">Qatar</option>
                                <option value="Kuwait">Kuwait</option>
                                <option value="Oman">Oman</option>
                                <option value="Bahrain">Bahrain</option>
                                <option value="Jordan">Jordan</option>
                                <option value="Lebanon">Lebanon</option>
                                <option value="Tunisia">Tunisia</option>
                                <option value="Algeria">Algeria</option>
                                <option value="Libya">Libya</option>
                                <option value="Sudan">Sudan</option>
                                <option value="Ethiopia">Ethiopia</option>
                                <option value="Somalia">Somalia</option>
                                <option value="Tanzania">Tanzania</option>
                                <option value="Uganda">Uganda</option>
                                <option value="Rwanda">Rwanda</option>
                                <option value="Zimbabwe">Zimbabwe</option>
                                <option value="Zambia">Zambia</option>
                                <option value="Botswana">Botswana</option>
                                <option value="Namibia">Namibia</option>
                                <option value="Mozambique">Mozambique</option>
                                <option value="Angola">Angola</option>
                                <option value="Cameroon">Cameroon</option>
                                <option value="Ivory Coast">Côte d'Ivoire</option>
                                <option value="Senegal">Senegal</option>
                                <option value="Mali">Mali</option>
                                <option value="Burkina Faso">Burkina Faso</option>
                                <option value="Niger">Niger</option>
                                <option value="Chad">Chad</option>
                                <option value="Congo">Congo</option>
                                <option value="DRC">DR Congo</option>
                            </select>
                        </div>
                            <div class="col-md-6 mb-3">
                                <label>Order Notes (optional)</label>
                                <textarea name="notes" class="form-control" rows="3" placeholder="Any special instructions..."></textarea>
                            </div>
                        </div>

                    </div>

                    {{-- Shipping Method Section --}}
                    <div class="checkout_details_area mt-30 clearfix" id="shippingSection" style="display:none;">
                        <div class="cart-page-heading mb-20">
                            <h5>Shipping Method</h5>
                            <p class="text-muted small">Select how you want your order delivered</p>
                        </div>

                        <div id="ratesLoading" class="text-center py-4" style="display:none;">
                            <div class="spinner-border text-success" role="status">
                                <span class="sr-only">Loading rates...</span>
                            </div>
                            <p class="mt-2 text-muted small">Fetching shipping rates...</p>
                        </div>

                        <div id="ratesMissingMessage" class="alert alert-info" style="display:none;">
                            <i class="fa fa-info-circle mr-2"></i>
                            Please fill in all delivery address details above to see available shipping rates.
                        </div>

                        <div id="ratesError" class="error-message-box" style="display:none;">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <i class="fa fa-exclamation-triangle text-danger mr-2"></i>
                                    <span id="errorMessageText"></span>
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-danger" onclick="retryFetchRates()">
                                    <i class="fa fa-refresh mr-1"></i> Retry
                                </button>
                            </div>
                        </div>

                        <div id="ratesList"></div>

                        <input type="hidden" name="shipping_service_code" id="selectedServiceCode">
                        <input type="hidden" name="shipping_carrier" id="selectedCarrier">
                        <input type="hidden" name="shipping_service_name" id="selectedServiceName">
                        <input type="hidden" name="shipping_fee" id="selectedShippingFee" value="0">
                        <input type="hidden" name="package_weight" id="packageWeight">
                        <input type="hidden" name="shipping_rate_data" id="shippingRateData">
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
                                    <span>
                                        {{ Str::limit($item['name'], 30) }} × {{ $item['quantity'] }}
                                        @if(isset($item['is_flash_sale']) && $item['is_flash_sale'])
                                            <span style="font-size:10px;background:#FADBD8;color:#E74C3C;padding:1px 6px;border-radius:8px;font-weight:700;margin-left:4px;">⚡ Flash</span>
                                        @endif
                                    </span>
                                    <span>₦{{ number_format($item['price'] * $item['quantity'], 2) }}</span>
                                </li>
                                @endforeach
                            <li>
                                <span>Subtotal</span>
                                <span id="displaySubtotal" data-value="{{ $subtotal }}">
                                    ₦{{ number_format($subtotal, 2) }}
                                </span>
                            </li>
                            <li>
                                <span>Shipping</span>
                                <span id="displayShippingFee" class="text-success">
                                    Select shipping
                                </span>
                            </li>
                            <li class="font-weight-bold">
                                <span>Total</span>
                                <span id="displayTotal" class="h5 mb-0">₦{{ number_format($subtotal, 2) }}</span>
                            </li>
                        </ul>

                        {{-- Payment Method --}}
                        <div class="cart-page-heading mb-20">
                            <h5>Payment Method</h5>
                        </div>

                        <div class="payment-methods mb-4">
                            <div class="card payment-method-card mb-3" id="walletMethodCard">
                                <div class="card-header bg-transparent border-bottom-0">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div class="d-flex align-items-center">
                                            <div class="custom-control custom-radio mr-3">
                                                <input type="radio" name="payment_method" id="paymentWallet" class="custom-control-input" value="wallet" checked>
                                                <label class="custom-control-label" for="paymentWallet">
                                                    <strong>Wallet Balance</strong>
                                                </label>
                                            </div>
                                        </div>
                                        <span class="badge badge-success">₦{{ number_format(auth('web')->user()->wallet_balance, 2) }} available</span>
                                    </div>
                                    <p class="text-muted small mt-2 mb-0 ml-4 pl-2">
                                        Pay instantly from your Orderer wallet. No redirects, no extra steps.
                                    </p>
                                    @if(auth('web')->user()->wallet_balance < $subtotal)
                                    <div class="mt-2 ml-4 pl-2">
                                        <div class="alert alert-warning py-2 px-3 mb-2" style="font-size:13px;">
                                            <i class="fa fa-exclamation-triangle mr-1"></i>
                                            Your wallet balance is insufficient for this order.
                                            You need at least <strong>₦{{ number_format($subtotal, 2) }}</strong>.
                                        </div>
                                        <a href="{{ route('buyer.wallet') }}" class="btn btn-sm btn-outline-success">
                                            <i class="fa fa-plus-circle mr-1"></i> Top Up Wallet
                                        </a>
                                    </div>
                                    @endif
                                </div>
                            </div>

                            <div class="card payment-method-card mb-3" id="korapayMethodCard">
                                <div class="card-header bg-transparent border-bottom-0">
                                    <div class="d-flex align-items-center">
                                        <div class="custom-control custom-radio mr-3">
                                            <input type="radio" name="payment_method" id="paymentKorapay" class="custom-control-input" value="korapay">
                                            <label class="custom-control-label" for="paymentKorapay">
                                                <strong>Pay with Korapay</strong>
                                            </label>
                                        </div>
                                    </div>
                                    <p class="text-muted small mt-2 mb-0 ml-4 pl-2">
                                        <i class="fa fa-lock mr-1" style="color:#2ECC71;"></i>
                                        Secure card or bank transfer payment. You will be redirected to complete your payment.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="custom-control custom-checkbox d-block mb-4">
                            <input type="checkbox" class="custom-control-input" id="terms" required>
                            <label class="custom-control-label" for="terms">
                                I agree to the <a href="{{ route('legal.terms') }}" style="color:#2ECC71;">Terms &amp; Conditions</a>
                            </label>
                        </div>

                        <button type="submit" class="btn essence-btn w-100" id="placeOrderBtn">
                            <i class="fa fa-shopping-bag mr-2"></i> Place Order
                        </button>

                    </div>
                </div>

            </div>
        </form>
        @endif
    </div>
</div>

<div id="orderLoader" class="order-loader">
    <div class="spinner-border text-success" role="status">
        <span class="sr-only">Processing...</span>
    </div>
    <p class="text-white mt-3 font-weight-bold">Processing your order, please wait...</p>
    <p class="text-white-50 small">Do not close or refresh this page</p>
</div>

@include('layouts.storefront.footer')

<script src="{{ asset('js/jquery/jquery-2.2.4.min.js') }}"></script>
<script src="{{ asset('js/popper.min.js') }}"></script>
<script src="{{ asset('js/bootstrap.min.js') }}"></script>
<script src="{{ asset('js/plugins.js') }}"></script>
<script src="{{ asset('js/classy-nav.min.js') }}"></script>
<script src="{{ asset('js/active.js') }}"></script>

<script>
// Pass PHP variable to JavaScript
const isBuyNow = {{ isset($isBuyNow) && $isBuyNow ? 'true' : 'false' }};
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';

// Payment method selection
document.getElementById('walletMethodCard').addEventListener('click', function() {
    document.getElementById('paymentWallet').checked = true;
    document.getElementById('walletMethodCard').classList.add('selected');
    document.getElementById('korapayMethodCard').classList.remove('selected');
});

document.getElementById('korapayMethodCard').addEventListener('click', function() {
    document.getElementById('paymentKorapay').checked = true;
    document.getElementById('korapayMethodCard').classList.add('selected');
    document.getElementById('walletMethodCard').classList.remove('selected');
});

// Function to check if all address fields are filled
function allAddressFieldsFilled() {
    const name = document.getElementById('shipping_name').value;
    const phone = document.getElementById('shipping_phone').value;
    const address = document.getElementById('shipping_address').value;
    const city = document.getElementById('shipping_city').value;
    const state = document.getElementById('shipping_state').value;
    const country = document.getElementById('countrySelect').value;
    
    return name && phone && address && city && state && country;
}

// Function to fetch shipping rates
function fetchShippingRates() {
    if (!allAddressFieldsFilled()) {
        document.getElementById('shippingSection').style.display = 'block';
        document.getElementById('ratesMissingMessage').style.display = 'block';
        document.getElementById('ratesLoading').style.display = 'none';
        return;
    }
    
    document.getElementById('ratesMissingMessage').style.display = 'none';
    document.getElementById('shippingSection').style.display = 'block';
    document.getElementById('ratesLoading').style.display = 'block';
    document.getElementById('ratesList').innerHTML = '';
    document.getElementById('ratesError').style.display = 'none';
    
    const formData = {
        shipping_name: document.getElementById('shipping_name').value,
        shipping_phone: document.getElementById('shipping_phone').value,
        shipping_address: document.getElementById('shipping_address').value,
        shipping_city: document.getElementById('shipping_city').value,
        shipping_state: document.getElementById('shipping_state').value,
        shipping_country: document.getElementById('countrySelect').value,
        shipping_zip: document.querySelector('[name="shipping_zip"]').value || '',
    };
    
    // DYNAMIC RATES ENDPOINT - Use the isBuyNow variable
    const ratesUrl = isBuyNow ? '{{ route("buy-now.rates") }}' : '{{ route("checkout.rates") }}';
    
    fetch(ratesUrl, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify(formData),
    })
    .then(response => response.json())
    .then(data => {
        document.getElementById('ratesLoading').style.display = 'none';

        if (!data.success || !data.seller_rates || data.seller_rates.length === 0) {
            document.getElementById('ratesList').innerHTML =
                '<div class="alert alert-warning">No shipping rates available. Please check your address.</div>';
            return;
        }

        let html = '';

        data.seller_rates.forEach(group => {
            // Show seller header only when there are multiple sellers
            if (data.multi_seller) {
                html += `
                    <p class="font-weight-bold mb-2 mt-3" style="color:#2ECC71;font-size:13px;">
                        <i class="fa fa-store mr-1"></i>
                        Ships from: <strong>${group.seller_name}</strong>
                        <span class="text-muted">(₦${parseFloat(group.subtotal).toFixed(2)})</span>
                    </p>`;
            }

            group.couriers.forEach((rate, index) => {
                const price     = parseFloat(rate.total || 0).toFixed(2);
                const radioName = `shipping_rate_${group.seller_id}`;

                html += `
                    <label class="d-block rate-card rounded p-3 mb-2"
                           data-seller-id="${group.seller_id}" style="cursor:pointer;">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center">
                                ${rate.courier_image
                                    ? `<img src="${rate.courier_image}" style="height:28px;width:auto;margin-right:10px;object-fit:contain;">`
                                    : ''}
                                <input type="radio" name="${radioName}"
                                       class="seller-rate-radio mr-3"
                                       data-seller-id="${group.seller_id}"
                                       data-price="${price}"
                                       data-carrier="${rate.courier_name || ''}"
                                       data-service="${rate.service_type || ''}"
                                       data-ratedata='${JSON.stringify(rate).replace(/'/g, "\\'")}'
                                       ${index === 0 ? 'checked' : ''}>
                                <div>
                                    <p class="mb-0 font-weight-bold">${rate.courier_name || 'Courier'}</p>
                                    <p class="mb-0 text-muted small">${rate.service_type || 'Standard'}</p>
                                    ${rate.delivery_eta
                                        ? `<small class="text-muted"><i class="fa fa-clock-o mr-1"></i>${rate.delivery_eta}</small>`
                                        : ''}
                                </div>
                            </div>
                            <span class="h5 mb-0 text-success">₦${price}</span>
                        </div>
                    </label>`;
            });

            if (data.multi_seller) html += '<hr class="my-2">';
        });

        document.getElementById('ratesList').innerHTML = html;

        // ── Recalc total shipping across all seller selections ───────────
        function recalcShipping() {
            let totalShipping  = 0;
            const allRateData  = {};
            let   firstCarrier = '';
            let   firstService = '';
            let   firstCode    = '';

            data.seller_rates.forEach(group => {
                const checked = document.querySelector(
                    `input[name="shipping_rate_${group.seller_id}"]:checked`
                );
                if (!checked) return;

                totalShipping += parseFloat(checked.dataset.price);
                allRateData[group.seller_id] = JSON.parse(checked.dataset.ratedata);

                if (!firstCarrier) {
                    firstCarrier = checked.dataset.carrier;
                    firstService = checked.dataset.service;
                    firstCode    = checked.value;
                }
            });

            const subtotal = parseFloat(
                document.getElementById('displaySubtotal').dataset.value || 0
            );

            document.getElementById('selectedShippingFee').value  = totalShipping.toFixed(2);
            document.getElementById('selectedCarrier').value      = firstCarrier;
            document.getElementById('selectedServiceName').value  = firstService;
            document.getElementById('selectedServiceCode').value  = firstCode;

            // For multi-seller this is the full map { seller_id: rate_object }
            // For single-seller this is { seller_id: rate_object } too — backend handles both
            document.getElementById('shippingRateData').value = JSON.stringify(allRateData);

            document.getElementById('displayShippingFee').textContent =
                '₦' + totalShipping.toFixed(2);
            document.getElementById('displayTotal').textContent =
                '₦' + (subtotal + totalShipping).toFixed(2);
        }

        // Attach change listeners
        document.querySelectorAll('.seller-rate-radio').forEach(radio => {
            radio.addEventListener('change', () => {
                const sellerId = radio.dataset.sellerId;
                document.querySelectorAll(`.rate-card[data-seller-id="${sellerId}"]`)
                    .forEach(c => c.classList.remove('selected'));
                radio.closest('.rate-card').classList.add('selected');
                recalcShipping();
            });
        });

        // Trigger initial calc with defaults
        recalcShipping();

        // Highlight default selected cards
        document.querySelectorAll('.seller-rate-radio:checked').forEach(radio => {
            radio.closest('.rate-card')?.classList.add('selected');
        });
    })
    .catch(error => {
        console.error('Fetch error:', error);
        document.getElementById('ratesLoading').style.display = 'none';
        document.getElementById('ratesError').style.display = 'block';
        document.getElementById('errorMessageText').textContent = 'Network error. Please try again.';
    });
}

function retryFetchRates() {
    fetchShippingRates();
}

// Add event listeners to address fields
const addressInputs = ['shipping_name', 'shipping_phone', 'shipping_address', 'shipping_city', 'shipping_state', 'countrySelect'];
let fetchTimeout;

addressInputs.forEach(id => {
    const element = document.getElementById(id);
    if (element) {
        element.addEventListener('input', () => {
            clearTimeout(fetchTimeout);
            fetchTimeout = setTimeout(fetchShippingRates, 1000);
        });
        element.addEventListener('change', () => {
            clearTimeout(fetchTimeout);
            fetchTimeout = setTimeout(fetchShippingRates, 500);
        });
    }
});

// Initial fetch if fields are pre-filled
setTimeout(() => {
    if (allAddressFieldsFilled()) {
        fetchShippingRates();
    }
}, 1000);

// Form submission with loader
document.getElementById('checkoutForm').addEventListener('submit', function(e) {
    const shippingSelected = document.getElementById('selectedServiceCode').value;
    
    if (!shippingSelected) {
        e.preventDefault();
        alert('Please select a shipping method.');
        return;
    }
    
    if (!document.getElementById('terms').checked) {
        e.preventDefault();
        alert('Please agree to the Terms & Conditions.');
        return;
    }
    
    document.getElementById('orderLoader').style.display = 'flex';
    document.getElementById('placeOrderBtn').disabled = true;
});
</script>
</body>
</html>