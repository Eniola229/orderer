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
/* Replace your existing pac-container styles with this */
.pac-container {
    border: 1px solid #dee2e6;
    border-radius: 8px;
    box-shadow: 0 4px 16px rgba(0,0,0,0.1);
    font-family: inherit;
    margin-top: 4px;
    z-index: 99999 !important;
    pointer-events: auto !important;
}
.pac-item {
    padding: 10px 14px;
    font-size: 14px;
    cursor: pointer;
    pointer-events: auto !important;
}
.pac-item:hover { background: #f0faf5; }
.pac-matched { color: #2ECC71; font-weight: 600; }
.pac-icon { display: none; }

.guarantee-item {
    transition: all 0.2s ease;
    cursor: default;
}
.guarantee-item:hover {
    background: #fff !important;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    transform: translateX(2px);
}
/* Shipping price styling with strikethrough */
.shipping-price-wrapper {
    text-align: right;
}

.shipping-original-price {
    display: block;
    font-size: 12px;
    color: #999;
    text-decoration: line-through;
    text-decoration-color: #dc3545;
    text-decoration-thickness: 2px;
    margin-bottom: 2px;
}

.shipping-discounted-price {
    display: inline-block;
    font-size: 18px;
    font-weight: bold;
    color: #28a745;
}

.shipping-save-badge {
    display: inline-block;
    background-color: #28a745;
    color: white;
    font-size: 10px;
    padding: 2px 6px;
    border-radius: 4px;
    margin-left: 6px;
    vertical-align: middle;
}

.shipping-free-badge {
    display: inline-block;
    background-color: #28a745;
    color: white;
    font-size: 12px;
    padding: 2px 8px;
    border-radius: 4px;
    font-weight: bold;
}

.shipping-price-normal {
    font-size: 18px;
    font-weight: bold;
    color: #28a745;
}

/* Courier card price container */
.rate-card .price-container {
    text-align: right;
}

/* Ensure strikethrough works across all browsers */
del, .strikethrough {
    text-decoration: line-through;
    text-decoration-color: #dc3545;
    text-decoration-thickness: 2px;
    color: #999;
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
                        <small>No, Street, city, state, country - We advice copying your address directly from google map, also make sure to add country</small>
                        <div class="d-flex align-items-center" style="gap:8px;">
                            <input type="text" class="form-control" id="shipping_address" name="shipping_address" placeholder="House number, street name, city, state, country" required>
                        </div>
                        <button type="button" id="useMyLocationBtn" class="btn btn-sm btn-outline-success mt-2" style="display:none;">
                            <i class="fa fa-map-marker mr-1"></i> Use my current location
                        </button>
                        <div id="locationLoading" class="small text-muted mt-1" style="display:none;">
                            <i class="fa fa-spinner fa-spin mr-1"></i> Getting your location...
                        </div>
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
<!--                             <div class="card payment-method-card mb-3" id="monnifyMethodCard">
                                <div class="card-header bg-transparent border-bottom-0">
                                    <div class="d-flex align-items-center">
                                        <div class="custom-control custom-radio mr-3">
                                            <input type="radio" name="payment_method" id="paymentMonnify" class="custom-control-input" value="monnify">
                                            <label class="custom-control-label" for="paymentMonnify">
                                                <strong>Pay with Monnify</strong>
                                            </label>
                                        </div>
                                    </div>
                                    <p class="text-muted small mt-2 mb-0 ml-4 pl-2">
                                        <i class="fa fa-lock mr-1" style="color:#2ECC71;"></i>
                                        Card · Bank Transfer · USSD · Phone Number
                                    </p>
                                </div>
                            </div> -->
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
                                        Card or bank transfer payment. You will be redirected to complete your payment.
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

                        {{-- Delivery Guarantee Section --}}
                            <div class="delivery-guarantee mt-2 mb-4">
                                <div class="cart-page-heading mb-3">
                                    <h5 class="mb-1">Delivery Guarantee</h5>
                                    <p class="text-muted small">We've got you covered with our buyer protection</p>
                                </div>
                                
                                <div class="guarantee-grid" style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 12px;">
                                    <div class="guarantee-item d-flex align-items-center p-3 rounded" style="background: #f8f9fa; border-left: 3px solid #E74C3C;">
                                        <i class="fa fa-exchange" style="font-size: 20px; color: #E74C3C; width: 32px;"></i>
                                        <div>
                                            <p class="mb-0 fw-semibold" style="font-size: 14px;">Return if damaged</p>
                                            <small class="text-muted">Item arrives damaged</small>
                                        </div>
                                    </div>
                                    
                                    <div class="guarantee-item d-flex align-items-center p-3 rounded" style="background: #f8f9fa; border-left: 3px solid #F39C12;">
                                        <i class="fa fa-calendar-times-o" style="font-size: 20px; color: #F39C12; width: 32px;"></i>
                                        <div>
                                            <p class="mb-0 fw-semibold" style="font-size: 14px;">15-day refund</p>
                                            <small class="text-muted">If no update on order</small>
                                        </div>
                                    </div>
                                    
                                    <div class="guarantee-item d-flex align-items-center p-3 rounded" style="background: #f8f9fa; border-left: 3px solid #8E44AD;">
                                        <i class="fa fa-clock-o" style="font-size: 20px; color: #8E44AD; width: 32px;"></i>
                                        <div>
                                            <p class="mb-0 fw-semibold" style="font-size: 14px;">₦1,000 credit</p>
                                            <small class="text-muted">For delivery delay</small>
                                        </div>
                                    </div>
                                    
                                    <div class="guarantee-item d-flex align-items-center p-3 rounded" style="background: #f8f9fa; border-left: 3px solid #2ECC71;">
                                        <i class="fa fa-undo" style="font-size: 20px; color: #2ECC71; width: 32px;"></i>
                                        <div>
                                            <p class="mb-0 fw-semibold" style="font-size: 14px;">60-day refund</p>
                                            <small class="text-muted">If no delivery</small>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mt-3 p-2 text-center" style="background: #e8f5e9; border-radius: 8px;">
                                    <i class="fa fa-shield" style="color: #2ECC71; margin-right: 6px;"></i>
                                    <small>Orderer Protection covers eligible purchases. <a href="{{ route('legal.buyer-terms') }}" style="color: #2ECC71; text-decoration: none;">Learn more</a></small>
                                </div>
                            </div>


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
<script type="text/javascript" src="https://sdk.monnify.com/plugin/monnify.js"></script>
<script>
const isBuyNow = {{ isset($isBuyNow) && $isBuyNow ? 'true' : 'false' }};
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';

// ── Google Places Autocomplete ───────────────────────────────────
function initAutocomplete() {
    const addressInput  = document.getElementById('shipping_address');
    const cityInput     = document.getElementById('shipping_city');
    const stateInput    = document.getElementById('shipping_state');
    const countrySelect = document.getElementById('countrySelect');

    if (!addressInput) return;

    const autocomplete = new google.maps.places.Autocomplete(addressInput, {
        types: ['address'],
        fields: ['address_components', 'formatted_address'],
    });

    // ── Prevent Enter from submitting form when dropdown open ──
    addressInput.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            // Close the dropdown manually on Enter
            const pacContainer = document.querySelector('.pac-container');
            if (pacContainer) {
                pacContainer.style.display = 'none';
            }
        }
    });

    autocomplete.addListener('place_changed', function () {
        const place = autocomplete.getPlace();
        
        // Close the dropdown immediately
        const pacContainers = document.querySelectorAll('.pac-container');
        pacContainers.forEach(container => {
            container.style.display = 'none';
        });
        
        // Remove focus from address input to ensure dropdown stays closed
        addressInput.blur();
        
        if (!place || !place.address_components) return;

        let streetNumber = '', route = '', city = '', state = '', country = '', zip = '';

        place.address_components.forEach(component => {
            const types = component.types;
            if (types.includes('street_number'))                       streetNumber = component.long_name;
            if (types.includes('route'))                               route        = component.long_name;
            if (types.includes('locality') ||
                types.includes('administrative_area_level_2'))         city         = component.long_name;
            if (types.includes('administrative_area_level_1'))         state        = component.long_name;
            if (types.includes('country'))                             country      = component.long_name;
            if (types.includes('postal_code'))                         zip          = component.long_name;
        });

        // Build full address
        let fullAddress = '';
        if (streetNumber) fullAddress += streetNumber + ' ';
        if (route) fullAddress += route + ', ';
        if (city) fullAddress += city + ', ';
        if (state) fullAddress += state + ', ';
        if (country) fullAddress += country;
        if (zip) fullAddress += ', ' + zip;
        
        // Set the formatted address or constructed address
        addressInput.value = place.formatted_address || fullAddress;

        if (city && cityInput) cityInput.value = city;
        if (state && stateInput) stateInput.value = state;
        if (zip) {
            const zipInput = document.querySelector('[name="shipping_zip"]');
            if (zipInput) zipInput.value = zip;
        }

        // Auto-select country dropdown
        if (country && countrySelect) {
            const match = Array.from(countrySelect.options).find(o =>
                o.value.toLowerCase()       === country.toLowerCase() ||
                o.textContent.toLowerCase() === country.toLowerCase()
            );
            if (match) countrySelect.value = match.value;
        }

        // Trigger input events so the rate fetcher picks up the changes
        [addressInput, cityInput, stateInput, countrySelect].forEach(el => {
            if (el) el.dispatchEvent(new Event('input', { bubbles: true }));
        });

        // Fetch rates after selection
        clearTimeout(fetchTimeout);
        fetchTimeout = setTimeout(fetchShippingRates, 600);
    });
    
    // Additional fix: Hide dropdown when clicking outside or pressing Escape
    document.addEventListener('click', function(e) {
        if (!addressInput.contains(e.target)) {
            const pacContainers = document.querySelectorAll('.pac-container');
            pacContainers.forEach(container => {
                container.style.display = 'none';
            });
        }
    });
    
    // Close dropdown on Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const pacContainers = document.querySelectorAll('.pac-container');
            pacContainers.forEach(container => {
                container.style.display = 'none';
            });
        }
    });
}

// ── Use my current location ──────────────────────────────────────
(function() {
    const STORAGE_KEY = 'ord_location_coords';
    const btn = document.getElementById('useMyLocationBtn');
    const loadingEl = document.getElementById('locationLoading');
    if (!btn) return;

    // Show the button if we already have stored coords, or if geolocation exists at all
    function maybeShowButton() {
        if (navigator.geolocation) {
            btn.style.display = 'inline-block';
        }
    }
    document.addEventListener('DOMContentLoaded', maybeShowButton);

    function fillFromGeocodeResult(result) {
        let streetNumber = '', route = '', city = '', state = '', country = '', zip = '';

        result.address_components.forEach(component => {
            const types = component.types;
            if (types.includes('street_number')) streetNumber = component.long_name;
            if (types.includes('route')) route = component.long_name;
            if (types.includes('locality') || types.includes('administrative_area_level_2')) city = component.long_name;
            if (types.includes('administrative_area_level_1')) state = component.long_name;
            if (types.includes('country')) country = component.long_name;
            if (types.includes('postal_code')) zip = component.long_name;
        });

        let fullAddress = '';
        if (streetNumber) fullAddress += streetNumber + ' ';
        if (route) fullAddress += route + ', ';
        if (city) fullAddress += city + ', ';
        if (state) fullAddress += state + ', ';
        if (country) fullAddress += country;
        if (zip) fullAddress += ', ' + zip;

        const addressInput  = document.getElementById('shipping_address');
        const cityInput     = document.getElementById('shipping_city');
        const stateInput    = document.getElementById('shipping_state');
        const countrySelect = document.getElementById('countrySelect');
        const zipInput      = document.querySelector('[name="shipping_zip"]');

        addressInput.value = result.formatted_address || fullAddress;
        if (city) cityInput.value = city;
        if (state) stateInput.value = state;
        if (zip && zipInput) zipInput.value = zip;

        if (country && countrySelect) {
            const match = Array.from(countrySelect.options).find(o =>
                o.value.toLowerCase() === country.toLowerCase() ||
                o.textContent.toLowerCase() === country.toLowerCase()
            );
            if (match) countrySelect.value = match.value;
        }

        [addressInput, cityInput, stateInput, countrySelect].forEach(el => {
            if (el) el.dispatchEvent(new Event('input', { bubbles: true }));
        });

        clearTimeout(fetchTimeout);
        fetchTimeout = setTimeout(fetchShippingRates, 600);
    }

    function reverseGeocode(lat, lng) {
        loadingEl.style.display = 'block';
        const geocoder = new google.maps.Geocoder();
        geocoder.geocode({ location: { lat, lng } }, (results, status) => {
            loadingEl.style.display = 'none';
            if (status === 'OK' && results && results[0]) {
                fillFromGeocodeResult(results[0]);
            } else {
                alert('Could not determine your address. Please type it in manually.');
            }
        });
    }

    btn.addEventListener('click', function() {
        const stored = localStorage.getItem(STORAGE_KEY);
        if (stored) {
            const { lat, lng } = JSON.parse(stored);
            reverseGeocode(lat, lng);
            return;
        }

        if (!navigator.geolocation) {
            alert('Geolocation is not supported by your browser.');
            return;
        }

        loadingEl.style.display = 'block';
        navigator.geolocation.getCurrentPosition(
            pos => {
                localStorage.setItem(STORAGE_KEY, JSON.stringify({
                    lat: pos.coords.latitude, lng: pos.coords.longitude, ts: Date.now()
                }));
                reverseGeocode(pos.coords.latitude, pos.coords.longitude);
            },
            err => {
                loadingEl.style.display = 'none';
                alert('Location permission denied. Please type your address manually.');
            }
        );
    });
})();

document.addEventListener('DOMContentLoaded', function() {
    if (typeof google !== 'undefined') {
        initAutocomplete();
    }
});

// ── Payment method selection ─────────────────────────────────────
const monnifyCard = document.getElementById('monnifyMethodCard');
const walletCard = document.getElementById('walletMethodCard');
const korapayCard = document.getElementById('korapayMethodCard');

if (walletCard) {
    walletCard.addEventListener('click', function() {
        const paymentWallet = document.getElementById('paymentWallet');
        if (paymentWallet) paymentWallet.checked = true;
        walletCard.classList.add('selected');
        if (korapayCard) korapayCard.classList.remove('selected');
        if (monnifyCard) monnifyCard.classList.remove('selected');
    });
}

if (korapayCard) {
    korapayCard.addEventListener('click', function() {
        const paymentKorapay = document.getElementById('paymentKorapay');
        if (paymentKorapay) paymentKorapay.checked = true;
        korapayCard.classList.add('selected');
        if (walletCard) walletCard.classList.remove('selected');
        if (monnifyCard) monnifyCard.classList.remove('selected');
    });
}

// ── Check all address fields filled ─────────────────────────────
function allAddressFieldsFilled() {
    const name    = document.getElementById('shipping_name').value;
    const phone   = document.getElementById('shipping_phone').value;
    const address = document.getElementById('shipping_address').value;
    const city    = document.getElementById('shipping_city').value;
    const state   = document.getElementById('shipping_state').value;
    const country = document.getElementById('countrySelect').value;
    return name && phone && address && city && state && country;
}

// ── Fetch shipping rates ─────────────────────────────────────────
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
        shipping_name:    document.getElementById('shipping_name').value,
        shipping_phone:   document.getElementById('shipping_phone').value,
        shipping_address: document.getElementById('shipping_address').value,
        shipping_city:    document.getElementById('shipping_city').value,
        shipping_state:   document.getElementById('shipping_state').value,
        shipping_country: document.getElementById('countrySelect').value,
        shipping_zip:     document.querySelector('[name="shipping_zip"]').value || '',
    };

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

        // ── Recalc total shipping ────────────────────────────────
        function recalcShipping() {
            let totalShipping = 0;
            const allRateData = {};
            let firstCarrier = '';
            let firstService = '';
            let firstCode = '';

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
                    firstCode = checked.value;
                }
            });

            const subtotal = parseFloat(
                document.getElementById('displaySubtotal').dataset.value || 0
            );

            // --- FREE SHIPPING CALCULATION ---
            const freeRule = data.free_shipping_rule;
            let discount = 0;
            let finalFee = totalShipping;
            let isFullyFree = false;

            if (freeRule) {
                const maxDisc = freeRule.max_discount || totalShipping;
                discount = Math.min(totalShipping, maxDisc);
                finalFee = totalShipping - discount;
                isFullyFree = finalFee <= 0;
                
                if (finalFee <= 0) {
                    finalFee = 0;
                    isFullyFree = true;
                }
            }

            // Store the actual fee to be paid (after discount)
            document.getElementById('selectedShippingFee').value = finalFee.toFixed(2);
            document.getElementById('selectedCarrier').value = firstCarrier;
            document.getElementById('selectedServiceName').value = firstService;
            document.getElementById('selectedServiceCode').value = firstCode;
            document.getElementById('shippingRateData').value = JSON.stringify(allRateData);

            // --- UPDATE EACH COURIER CARD WITH PROPER CSS ---
            document.querySelectorAll('.rate-card').forEach(card => {
                const priceSpan = card.querySelector('.h5.mb-0.text-success');
                const radio = card.querySelector('.seller-rate-radio');
                
                if (radio && priceSpan) {
                    const originalPrice = parseFloat(radio.dataset.price);
                    
                    // Calculate proportional discount for this courier
                    const proportion = originalPrice / totalShipping;
                    const discountedPrice = originalPrice - (discount * proportion);
                    const isThisCourierFree = discountedPrice <= 0;
                    
                    if (isFullyFree || isThisCourierFree) {
                        // FULLY FREE
                        priceSpan.innerHTML = `
                            <div class="shipping-price-wrapper">
                                <span class="shipping-original-price">₦${originalPrice.toFixed(2)}</span>
                                <span class="shipping-discounted-price">FREE</span>
                                <span class="shipping-free-badge">🎉 FREE Shipping</span>
                            </div>
                        `;
                    } else if (discount > 0 && discountedPrice < originalPrice) {
                        // PARTIAL DISCOUNT - with strikethrough
                        const savedAmount = (originalPrice - discountedPrice).toFixed(2);
                        priceSpan.innerHTML = `
                            <div class="shipping-price-wrapper">
                                <span class="shipping-original-price">₦${originalPrice.toFixed(2)}</span>
                                <span class="shipping-discounted-price">₦${discountedPrice.toFixed(2)}</span>
                                <span class="shipping-save-badge">Save ₦${savedAmount}</span>
                            </div>
                        `;
                    } else {
                        // NO DISCOUNT
                        priceSpan.innerHTML = `<span class="shipping-price-normal">₦${originalPrice.toFixed(2)}</span>`;
                    }
                }
            });

            // --- UPDATE ORDER SUMMARY WITH PROPER CSS ---
            if (isFullyFree) {
                document.getElementById('displayShippingFee').innerHTML = `
                    <div class="shipping-price-wrapper">
                        <span class="shipping-original-price">₦${totalShipping.toFixed(2)}</span>
                        <span class="shipping-discounted-price">FREE Shipping</span>
                        <span class="shipping-free-badge">${freeRule?.name || 'Free Shipping'}</span>
                    </div>
                `;
            } else if (discount > 0) {
                const savedAmount = (totalShipping - finalFee).toFixed(2);
                document.getElementById('displayShippingFee').innerHTML = `
                    <div class="shipping-price-wrapper">
                        <span class="shipping-original-price">₦${totalShipping.toFixed(2)}</span>
                        <span class="shipping-discounted-price">₦${finalFee.toFixed(2)}</span>
                        <span class="shipping-save-badge">Save ₦${savedAmount}</span>
                    </div>
                `;
            } else {
                document.getElementById('displayShippingFee').innerHTML = `<span class="shipping-price-normal">₦${totalShipping.toFixed(2)}</span>`;
            }

            // --- UPDATE TOTAL ---
            const grandTotal = subtotal + finalFee;
            document.getElementById('displayTotal').innerHTML = `<span class="h5 mb-0">₦${grandTotal.toFixed(2)}</span>`;
        }

        // Attach change listeners to rate radios
        document.querySelectorAll('.seller-rate-radio').forEach(radio => {
            radio.addEventListener('change', () => {
                const sellerId = radio.dataset.sellerId;
                document.querySelectorAll(`.rate-card[data-seller-id="${sellerId}"]`)
                    .forEach(c => c.classList.remove('selected'));
                radio.closest('.rate-card').classList.add('selected');
                recalcShipping();
            });
        });

        // Initial calc with defaults
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

// ── Watch address fields for changes ────────────────────────────
const addressInputIds = ['shipping_name', 'shipping_phone', 'shipping_address', 'shipping_city', 'shipping_state', 'countrySelect'];
let fetchTimeout;

addressInputIds.forEach(id => {
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

// Initial fetch if fields already pre-filled
setTimeout(() => {
    if (allAddressFieldsFilled()) {
        fetchShippingRates();
    }
}, 1000);

// ── Form submission ──────────────────────────────────────────────
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

    const paymentMethod = document.querySelector('input[name="payment_method"]:checked').value;

    if (paymentMethod === 'monnify') {
        e.preventDefault();
        if (typeof window.MonnifySDK === 'undefined') {
            alert('Monnify is still loading — please try again in a moment.');
            return;
        }
        launchMonnifyCheckout();
        return;
    }

    document.getElementById('orderLoader').style.display = 'flex';
    document.getElementById('placeOrderBtn').disabled = true;
});

function launchMonnifyCheckout() {
    const btn = document.getElementById('placeOrderBtn');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm mr-2"></span> Initializing...';

    const form     = document.getElementById('checkoutForm');
    const formData = new FormData(form);
    // Force monnify so the server returns JSON config
    formData.set('payment_method', 'monnify');

    const actionUrl = isBuyNow ? '{{ route("buy-now.place") }}' : '{{ route("checkout.place") }}';
    const verifyUrl = isBuyNow ? '{{ route("buy-now.monnify.verify") }}' : '{{ route("checkout.monnify.verify") }}';

    fetch(actionUrl, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Accept':       'application/json',
            'X-Requested-With': 'XMLHttpRequest',
        },
        body: formData,
    })
    .then(res => {
        if (!res.ok) return res.text().then(t => { throw new Error('Server error: ' + t.substring(0, 200)); });
        return res.json();
    })
    .then(data => {
        btn.disabled = false;
        btn.innerHTML = '<i class="fa fa-shopping-bag mr-2"></i> Place Order';

        if (!data.paymentReference) {
            alert(data.message || 'Could not initialize payment. Please try again.');
            return;
        }

        window.MonnifySDK.initialize({
            amount:             data.amount,
            currency:           'NGN',
            reference:          data.paymentReference,
            customerFullName:   data.customerName,
            customerEmail:      data.email,
            apiKey:             data.apiKey,
            contractCode:       data.contractCode,
            paymentDescription: 'Order Payment',
            isTestMode:         {{ app()->environment('production') ? 'false' : 'true' }},

            onComplete: function(response) {
                const ref = (response && response.paymentReference)
                    ? response.paymentReference : data.paymentReference;
                verifyMonnifyOrder(ref, data.orderId, verifyUrl);
            },
            onClose: function(closeData) {
                if (closeData && closeData.paymentStatus === 'USER_CANCELLED') return;
                if (closeData && closeData.paymentReference) {
                    verifyMonnifyOrder(closeData.paymentReference, data.orderId, verifyUrl);
                }
            },
        });
    })
    .catch(err => {
        btn.disabled = false;
        btn.innerHTML = '<i class="fa fa-shopping-bag mr-2"></i> Place Order';
        alert(err.message || 'Could not initialize payment. Please try again.');
    });
}

function verifyMonnifyOrder(reference, orderId, verifyUrl) {
    // Show a simple overlay
    document.getElementById('orderLoader').style.display = 'flex';
    document.querySelector('#orderLoader p:first-of-type').textContent = 'Verifying payment...';

    fetch(verifyUrl, {
        method:  'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept':       'application/json',
        },
        body: JSON.stringify({ reference: reference, order_id: orderId }),
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            window.location.href = data.redirect_url;
        } else {
            document.getElementById('orderLoader').style.display = 'none';
            alert(data.message || 'Payment verification failed. Contact support if you were charged.');
        }
    })
    .catch(() => {
        document.getElementById('orderLoader').style.display = 'none';
        alert('Verification error. Contact support if you were charged.');
    });
}
</script>
</body>
</html>