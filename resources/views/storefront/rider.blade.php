@auth('web')@include('layouts.storefront.header-auth')@else @include('layouts.storefront.header-guest')@endauth
@include('layouts.storefront.cart-sidebar')
@include('layouts.partials.alerts')

<style>
@import url('https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600;700&family=DM+Mono:wght@400;500&display=swap');

:root {
    --green:       #1DB954;
    --green-dark:  #158a3e;
    --green-glow:  rgba(29,185,84,0.15);
    --bg:          #0a0a0a;
    --surface:     #141414;
    --surface2:    #1c1c1c;
    --border:      #2a2a2a;
    --border-active: #1DB954;
    --text:        #f0f0f0;
    --text-muted:  #888;
    --text-dim:    #555;
    --red:         #e74c3c;
    --radius:      14px; 
    --radius-sm:   8px;
}

* { box-sizing: border-box; margin: 0; padding: 0; }

body { background: var(--bg); font-family: 'DM Sans', sans-serif; color: var(--text); }

/* ── Page wrapper ── */
.booking-page {
    min-height: 100vh;
    padding: 40px 0 80px;
}

.booking-shell {
    max-width: 680px;
    margin: 0 auto;
    padding: 0 20px;
}

/* ── Page header ── */
.booking-hero {
    text-align: center;
    margin-bottom: 40px;
}
.booking-hero h1 {
    font-size: clamp(26px, 4vw, 36px);
    font-weight: 700;
    letter-spacing: -0.5px;
    color: var(--text);
}
.booking-hero p {
    color: var(--text-muted);
    font-size: 15px;
    margin-top: 6px;
}

/* ── Step progress bar ── */
.progress-track {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0;
    margin-bottom: 40px;
    position: relative;
}

.progress-step {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 6px;
    position: relative;
    z-index: 1;
}

.progress-step .dot {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    border: 2px solid var(--border);
    background: var(--surface);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 13px;
    font-weight: 600;
    color: var(--text-dim);
    transition: all 0.35s cubic-bezier(0.4,0,0.2,1);
    font-family: 'DM Mono', monospace;
}

.progress-step .step-label {
    font-size: 11px;
    font-weight: 500;
    color: var(--text-dim);
    white-space: nowrap;
    transition: color 0.3s;
    letter-spacing: 0.3px;
}

.progress-step.active .dot {
    border-color: var(--green);
    background: var(--green);
    color: #fff;
    box-shadow: 0 0 0 6px var(--green-glow);
}
.progress-step.active .step-label { color: var(--green); font-weight: 600; }

.progress-step.done .dot {
    border-color: var(--green);
    background: var(--green);
    color: #fff;
}
.progress-step.done .step-label { color: var(--text-muted); }

.progress-connector {
    flex: 1;
    height: 2px;
    background: var(--border);
    max-width: 80px;
    margin-bottom: 22px;
    position: relative;
    overflow: hidden;
}
.progress-connector .fill {
    position: absolute;
    left: 0; top: 0;
    height: 100%;
    width: 0%;
    background: var(--green);
    transition: width 0.5s cubic-bezier(0.4,0,0.2,1);
}

/* ── Step panels ── */
.step-panel {
    display: none;
    animation: slideUp 0.4s cubic-bezier(0.4,0,0.2,1);
}
.step-panel.active { display: block; }

@keyframes slideUp {
    from { opacity: 0; transform: translateY(18px); }
    to   { opacity: 1; transform: translateY(0); }
}

/* ── Cards ── */
.card-block {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    padding: 28px;
    margin-bottom: 16px;
}

.card-block-title {
    font-size: 13px;
    font-weight: 600;
    letter-spacing: 1.2px;
    text-transform: uppercase;
    color: var(--text-muted);
    margin-bottom: 20px;
}

/* ── Form controls ── */
.field-group { margin-bottom: 18px; }
.field-group label {
    display: block;
    font-size: 13px;
    font-weight: 500;
    color: var(--text-muted);
    margin-bottom: 7px;
}
.field-group label span { color: var(--red); margin-left: 2px; }

.field-group input,
.field-group select,
.field-group textarea {
    width: 100%;
    background: var(--surface2);
    border: 1.5px solid var(--border);
    border-radius: var(--radius-sm);
    color: var(--text);
    font-family: 'DM Sans', sans-serif;
    font-size: 15px;
    padding: 11px 14px;
    outline: none;
    transition: border-color 0.2s, box-shadow 0.2s;
    appearance: none;
}
.field-group input:focus,
.field-group select:focus {
    border-color: var(--green);
    box-shadow: 0 0 0 3px var(--green-glow);
}
.field-group input::placeholder { color: var(--text-dim); }

.field-row { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
.field-row-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 14px; }

@media (max-width: 520px) {
    .field-row, .field-row-3 { grid-template-columns: 1fr; }
}

/* ── Delivery type picker ── */
.type-picker { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }

.type-option {
    border: 1.5px solid var(--border);
    border-radius: var(--radius-sm);
    padding: 16px;
    cursor: pointer;
    transition: all 0.2s;
    display: flex;
    align-items: center;
    gap: 12px;
}
.type-option:hover { border-color: var(--text-dim); }
.type-option.selected {
    border-color: var(--green);
    background: var(--green-glow);
}
.type-option .type-icon {
    font-size: 24px;
    line-height: 1;
}
.type-option .type-info p {
    font-size: 14px;
    font-weight: 600;
    color: var(--text);
    margin-bottom: 2px;
}
.type-option .type-info small {
    font-size: 12px;
    color: var(--text-muted);
}

/* ── Address columns ── */
.addr-cols {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
}


.addr-col-head {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 13px;
    font-weight: 600;
    margin-bottom: 16px;
}
.addr-col-head .dot-indicator {
    width: 10px; height: 10px;
    border-radius: 50%;
    flex-shrink: 0;
}

/* ── Rate cards ── */
.rate-card {
    border: 1.5px solid var(--border);
    border-radius: var(--radius-sm);
    padding: 16px;
    cursor: pointer;
    transition: all 0.2s;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    margin-bottom: 10px;
    background: var(--surface2);
}
.rate-card:hover { border-color: var(--text-dim); }
.rate-card.selected {
    border-color: var(--green);
    background: rgba(29,185,84,0.08);
}

.rate-card-left { display: flex; align-items: center; gap: 12px; }
.rate-card-logo {
    width: 40px; height: 40px;
    border-radius: 8px;
    background: var(--surface);
    display: flex; align-items: center; justify-content: center;
    overflow: hidden;
    flex-shrink: 0;
}
.rate-card-logo img { width: 100%; height: 100%; object-fit: contain; }
.rate-card-logo-fallback {
    width: 40px; height: 40px;
    border-radius: 8px;
    background: var(--green-glow);
    display: flex; align-items: center; justify-content: center;
    font-size: 18px;
    flex-shrink: 0;
}

.rate-card-name { font-size: 14px; font-weight: 600; color: var(--text); }
.rate-card-type { font-size: 12px; color: var(--text-muted); margin-top: 2px; }
.rate-card-eta  { font-size: 11px; color: var(--green); margin-top: 3px; font-weight: 500; }

.rate-card-price {
    font-size: 20px;
    font-weight: 700;
    color: var(--green);
    font-family: 'DM Mono', monospace;
    white-space: nowrap;
}

/* ── Loading spinner ── */
.rates-loading {
    display: none;
    text-align: center;
    padding: 32px;
}
.spinner {
    width: 32px; height: 32px;
    border: 3px solid var(--border);
    border-top-color: var(--green);
    border-radius: 50%;
    animation: spin 0.8s linear infinite;
    margin: 0 auto 12px;
}
@keyframes spin { to { transform: rotate(360deg); } }
.rates-loading p { font-size: 13px; color: var(--text-muted); }

/* ── Summary box ── */
.summary-box {
    background: var(--surface2);
    border: 1px solid var(--border);
    border-radius: var(--radius-sm);
    padding: 18px;
    margin-bottom: 20px;
}
.summary-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 7px 0;
    font-size: 14px;
}
.summary-row:not(:last-child) { border-bottom: 1px solid var(--border); }
.summary-row .label { color: var(--text-muted); }
.summary-row .value { font-weight: 600; color: var(--text); }
.summary-row .value.green { color: var(--green); font-size: 18px; font-family: 'DM Mono', monospace; }

/* ── Payment options ── */
.pay-option {
    border: 1.5px solid var(--border);
    border-radius: var(--radius-sm);
    padding: 16px;
    cursor: pointer;
    transition: all 0.2s;
    display: flex;
    align-items: center;
    gap: 14px;
    margin-bottom: 10px;
    background: var(--surface2);
}
.pay-option:hover { border-color: var(--text-dim); }
.pay-option.selected { border-color: var(--green); background: rgba(29,185,84,0.08); }
.pay-option .pay-icon {
    width: 38px; height: 38px;
    border-radius: 8px;
    background: var(--surface);
    display: flex; align-items: center; justify-content: center;
    font-size: 18px;
    flex-shrink: 0;
}
.pay-option .pay-label { font-size: 14px; font-weight: 600; color: var(--text); }
.pay-option .pay-sub   { font-size: 12px; color: var(--text-muted); margin-top: 2px; }
.pay-option .pay-badge {
    margin-left: auto;
    font-size: 12px;
    font-weight: 600;
    color: var(--green);
    background: var(--green-glow);
    padding: 3px 8px;
    border-radius: 20px;
}

/* ── Buttons ── */
.btn-primary-full {
    width: 100%;
    padding: 14px;
    background: var(--green);
    color: #fff;
    border: none;
    border-radius: var(--radius-sm);
    font-family: 'DM Sans', sans-serif;
    font-size: 15px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    letter-spacing: 0.2px;
}
.btn-primary-full:hover {
    background: var(--green-dark);
    transform: translateY(-1px);
    box-shadow: 0 6px 20px rgba(29,185,84,0.3);
}
.btn-primary-full:active { transform: translateY(0); }

.btn-secondary {
    background: transparent;
    border: 1.5px solid var(--border);
    color: var(--text-muted);
    border-radius: var(--radius-sm);
    padding: 11px 20px;
    font-family: 'DM Sans', sans-serif;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}
.btn-secondary:hover { border-color: var(--text-muted); color: var(--text); }

.btn-row {
    display: flex;
    gap: 12px;
    align-items: center;
}
.btn-row .btn-primary-full { flex: 1; }

/* ── Input group ── */
.input-with-prefix {
    display: flex;
    align-items: center;
    background: var(--surface2);
    border: 1.5px solid var(--border);
    border-radius: var(--radius-sm);
    overflow: hidden;
    transition: border-color 0.2s, box-shadow 0.2s;
}
.input-with-prefix:focus-within {
    border-color: var(--green);
    box-shadow: 0 0 0 3px var(--green-glow);
}
.input-with-prefix .prefix {
    padding: 11px 12px;
    font-size: 14px;
    color: var(--text-muted);
    background: var(--surface);
    border-right: 1px solid var(--border);
    white-space: nowrap;
}
.input-with-prefix input {
    border: none !important;
    box-shadow: none !important;
    background: transparent !important;
    flex: 1;
}

/* ── Alert ── */
.inline-alert {
    padding: 12px 16px;
    border-radius: var(--radius-sm);
    font-size: 13px;
    margin-bottom: 12px;
}
.inline-alert.warning { background: rgba(231,76,60,0.12); color: #e74c3c; border: 1px solid rgba(231,76,60,0.25); }
.inline-alert.info    { background: var(--green-glow); color: var(--green); border: 1px solid rgba(29,185,84,0.3); }

/* ── Guest lock ── */
.guest-lock {
    text-align: center;
    padding: 60px 20px;
}
.guest-lock .lock-icon {
    width: 64px; height: 64px;
    border-radius: 50%;
    background: var(--surface2);
    border: 1px solid var(--border);
    display: flex; align-items: center; justify-content: center;
    font-size: 26px;
    margin: 0 auto 20px;
}
.guest-lock h4 { font-size: 20px; font-weight: 700; margin-bottom: 8px; }
.guest-lock p  { color: var(--text-muted); font-size: 14px; margin-bottom: 24px; }

/* ── Custom select ── */
.custom-select-wrapper { position: relative; }
.custom-select-trigger {
    background: var(--surface2);
    border: 1.5px solid var(--border);
    border-radius: var(--radius-sm);
    padding: 11px 14px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
    transition: border-color 0.2s;
}
.custom-select-trigger:hover,
.custom-select-trigger.open { border-color: var(--green); }

.select-left { display: flex; align-items: center; gap: 10px; }
.select-icon { font-size: 18px; }
.select-text { font-size: 14px; font-weight: 500; color: var(--text); }
.select-sub  { font-size: 11px; color: var(--text-muted); }

.select-arrow { width: 16px; color: var(--text-muted); transition: transform 0.2s; flex-shrink: 0; }
.select-arrow.open { transform: rotate(180deg); }

.select-dropdown {
    position: absolute;
    top: calc(100% + 6px);
    left: 0; right: 0;
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: var(--radius-sm);
    z-index: 100;
    overflow: hidden;
    box-shadow: 0 8px 24px rgba(0,0,0,0.4);
}
.select-dropdown.hidden { display: none; }

.dropdown-item {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 12px 14px;
    cursor: pointer;
    transition: background 0.15s;
    position: relative;
}
.dropdown-item:hover { background: var(--surface2); }
.dropdown-item.selected { background: var(--green-glow); }
.dropdown-item .check { width: 16px; color: var(--green); margin-left: auto; }
.dropdown-divider { height: 1px; background: var(--border); }

/* ── Breadcrumb area override ── */
.breadcumb_area { display: none; }

/* ── Mobile responsiveness ─────────────────────────── */
@media (max-width: 640px) {

    .booking-shell {
        padding: 0 14px;
    }

    .booking-hero {
        margin-bottom: 28px;
    }

    .booking-hero h1 {
        font-size: 22px;
    }

    /* Shrink progress dots + labels on small screens */
    .progress-step .dot {
        width: 28px;
        height: 28px;
        font-size: 11px;
    }

    .progress-step .step-label {
        font-size: 9px;
    }

    .progress-connector {
        max-width: 40px;
    }

    /* Stack address columns vertically */
    .addr-cols {
        grid-template-columns: 1fr;
        gap: 0;
    }

    /* Add visual separator between pickup and delivery */
    .addr-cols > div:last-child {
        border-top: 1px solid var(--border);
        padding-top: 20px;
        margin-top: 4px;
    }

    /* Stack delivery type picker */
    .type-picker {
        grid-template-columns: 1fr;
        gap: 10px;
    }

    /* Stack all field rows */
    .field-row,
    .field-row-3 {
        grid-template-columns: 1fr;
        gap: 0;
    }

    /* Tighter card padding */
    .card-block {
        padding: 20px 16px;
    }

    /* Back + continue buttons: stack on very small screens */
    .btn-row {
        flex-wrap: wrap;
    }

    .btn-secondary {
        width: 100%;
        justify-content: center;
    }

    /* Rate cards: wrap price below courier info */
    .rate-card {
        flex-wrap: wrap;
        gap: 8px;
    }

    .rate-card-price {
        font-size: 17px;
        margin-left: auto;
    }

    /* Summary box rows */
    .summary-row .value.green {
        font-size: 16px;
    }

    /* Payment options */
    .pay-option {
        flex-wrap: wrap;
    }

    .pay-badge {
        width: 100%;
        margin-left: 0;
        margin-top: 6px;
        text-align: center;
    }
</style>

<div class="booking-page">
    <div class="booking-shell">

        <div class="booking-hero">
            <h1>Book a Delivery</h1>
            <p>Fast, reliable shipping powered by top couriers</p>
        </div>

        @auth('web')

        {{-- Progress bar --}}
        <div class="progress-track" id="progressTrack">
            <div class="progress-step active" id="pstep-1">
                <div class="dot">1</div>
                <span class="step-label">Package</span>
            </div>
            <div class="progress-connector"><div class="fill" id="conn-1"></div></div>
            <div class="progress-step" id="pstep-2">
                <div class="dot">2</div>
                <span class="step-label">Addresses</span>
            </div>
            <div class="progress-connector"><div class="fill" id="conn-2"></div></div>
            <div class="progress-step" id="pstep-3">
                <div class="dot">3</div>
                <span class="step-label">Rates</span>
            </div>
            <div class="progress-connector"><div class="fill" id="conn-3"></div></div>
            <div class="progress-step" id="pstep-4">
                <div class="dot">4</div>
                <span class="step-label">Payment</span>
            </div>
        </div>

        <form action="{{ route('rider.book') }}" method="POST" id="riderForm">
            @csrf

            {{-- ═══ STEP 1: Package Details ═══ --}}
            <div class="step-panel active" id="step-panel-1">

                <div class="card-block">
                    <div class="card-block-title">What are you sending?</div>

                    <div class="field-group">
                        <label>Delivery Type</label>
                        <div class="type-picker">
                            <div class="type-option selected" id="typeLocal" onclick="selectDeliveryType('local')">
                                <div class="type-icon">🏠</div>
                                <div class="type-info">
                                    <p>Local</p>
                                    <small>Within your country</small>
                                </div>
                            </div>
                            <div class="type-option" id="typeIntl" onclick="selectDeliveryType('international')">
                                <div class="type-icon">✈️</div>
                                <div class="type-info">
                                    <p>International</p>
                                    <small>Ship worldwide</small>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" name="delivery_type" id="deliveryType" value="local">
                    </div>

                    <div class="field-group">
                        <label>What are you sending? <span>*</span></label>
                        <input type="text" name="item_description" id="itemDescription"
                               placeholder="e.g. Electronics, Clothing, Documents">
                    </div>

                    <div class="field-row-3">
                        <div class="field-group">
                            <label>Weight (kg)</label>
                            <input type="number" name="weight_kg" id="weightKg"
                                   step="0.1" min="0.1" value="0.5">
                        </div>
                        <div class="field-group">
                            <label>Declared Value</label>
                            <div class="input-with-prefix">
                                <span class="prefix">$</span>
                                <input type="number" name="declared_value" step="0.01" min="0" value="10">
                            </div>
                        </div>
                        <div class="field-group">
                            <label>Currency</label>
                            <input type="text" value="NGN" disabled style="opacity:0.4;cursor:not-allowed;">
                        </div>
                    </div>
                </div>

                <div class="btn-row">
                    <button type="button" class="btn-primary-full" onclick="goStep(2)">
                        Continue
                        <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                            <path d="M6 3l5 5-5 5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </button>
                </div>
            </div>

            {{-- ═══ STEP 2: Addresses ═══ --}}
            <div class="step-panel" id="step-panel-2">
                <div class="card-block">
                    <div class="card-block-title">Pickup & Delivery</div>

                    <div class="addr-cols">
                        {{-- Pickup --}}
                        <div>
                            <div class="addr-col-head">
                                <div class="dot-indicator" style="background:var(--green);"></div>
                                Pickup
                            </div>

                            <div class="field-group">
                                <label>Sender Name <span>*</span></label>
                                <input type="text" name="sender_name"
                                       value="{{ auth('web')->user()->full_name }}">
                            </div>
                            <div class="field-group">
                                <label>Sender Phone <span>*</span></label>
                                <input type="tel" name="sender_phone"
                                       value="{{ auth('web')->user()->phone }}">
                            </div>
                            <div class="field-group">
                                <label>Street Address In Full (No Street, City, Country) <span>*</span></label>
                                <input type="text" name="pickup_address" placeholder="House no, street">
                            </div>
                            <div class="field-group">
                                <label>City <span>*</span></label>
                                <input type="text" name="pickup_city" placeholder="e.g. Lagos">
                            </div>
                            <div class="field-group">
                                <label>Country <span>*</span></label>
                                <select name="pickup_country" id="pickupCountry">
                                    <option value="">Loading…</option>
                                </select>
                            </div>
                        </div>

                        {{-- Delivery --}}
                        <div>
                            <div class="addr-col-head">
                                <div class="dot-indicator" style="background:var(--red);"></div>
                                Delivery
                            </div>

                            <div class="field-group">
                                <label>Recipient Name <span>*</span></label>
                                <input type="text" name="recipient_name" placeholder="Who is receiving?">
                            </div>
                            <div class="field-group">
                                <label>Recipient Phone <span>*</span></label>
                                <input type="tel" name="recipient_phone" placeholder="Recipient phone">
                            </div>
                            <div class="field-group">
                                <label>Street Address In Full (No Street, City, Country)<span>*</span></label>
                                <input type="text" name="delivery_address" placeholder="House no, street">
                            </div>
                            <div class="field-group">
                                <label>City <span>*</span></label>
                                <input type="text" name="delivery_city" placeholder="e.g. Abuja">
                            </div>
                            <div class="field-group">
                                <label>Country <span>*</span></label>
                                <select name="delivery_country" id="deliveryCountry">
                                    <option value="">Loading…</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="btn-row">
                    <button type="button" class="btn-secondary" onclick="goStep(1)">
                        <svg width="14" height="14" viewBox="0 0 16 16" fill="none">
                            <path d="M10 3L5 8l5 5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        Back
                    </button>
                    <button type="button" class="btn-primary-full" onclick="validateAddressesAndFetchRates()">
                        Get Shipping Rates
                        <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                            <path d="M6 3l5 5-5 5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </button>
                </div>
            </div>

            {{-- ═══ STEP 3: Select Rate ═══ --}}
            <div class="step-panel" id="step-panel-3">
                <div class="card-block">
                    <div class="card-block-title">Choose a courier</div>

                    <div class="rates-loading" id="riderRatesLoading">
                        <div class="spinner"></div>
                        <p>Finding the best rates for your route…</p>
                    </div>

                    <div id="riderRatesList"></div>

                    <input type="hidden" name="service_code" id="riderServiceCode">
                    <input type="hidden" name="carrier"      id="riderCarrier">
                    <input type="hidden" name="service_name" id="riderServiceName">
                    <input type="hidden" name="fee"          id="riderFee" value="0">
                    <input type="hidden" name="rate_data"    id="riderRateData">
                    <input type="hidden" name="courier_id"   id="riderCourierId"> 
                </div>

                <div class="btn-row">
                    <button type="button" class="btn-secondary" onclick="goStep(2)">
                        <svg width="14" height="14" viewBox="0 0 16 16" fill="none">
                            <path d="M10 3L5 8l5 5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        Back
                    </button>
                    <button type="button" class="btn-primary-full" onclick="proceedToPayment()">
                        Continue to Payment
                        <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                            <path d="M6 3l5 5-5 5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </button>
                </div>
            </div>

            {{-- ═══ STEP 4: Payment ═══ --}}
            <div class="step-panel" id="step-panel-4">

                <div class="card-block">
                    <div class="card-block-title">Order Summary</div>
                    <div class="summary-box">
                        <div class="summary-row">
                            <span class="label">Courier</span>
                            <span class="value" id="displayRiderCarrier">—</span>
                        </div>
                        <div class="summary-row">
                            <span class="label">Service</span>
                            <span class="value" id="displayRiderService">—</span>
                        </div>
                        <div class="summary-row">
                            <span class="label">Shipping Fee</span>
                            <span class="value" id="displayRiderFee">₦0.00</span>
                        </div>
                        <div class="summary-row">
                            <span class="label" style="display:flex;align-items:center;gap:6px;">
                                Service Fee
                                {{-- Tooltip icon --}}
                                <span id="sfTooltipBtn"
                                      style="display:inline-flex;align-items:center;justify-content:center;
                                             width:16px;height:16px;border-radius:50%;background:var(--text-dim);
                                             color:var(--text);font-size:10px;font-weight:700;cursor:pointer;
                                             position:relative;flex-shrink:0;"
                                      onmouseenter="document.getElementById('sfTooltip').style.display='block'"
                                      onmouseleave="document.getElementById('sfTooltip').style.display='none'">
                                    ?
                                    <span id="sfTooltip"
                                          style="display:none;position:absolute;bottom:130%;left:50%;
                                                 transform:translateX(-50%);background:#fff;color:#111;
                                                 border:1px solid var(--border);border-radius:8px;
                                                 padding:8px 12px;font-size:12px;font-weight:400;
                                                 white-space:nowrap;z-index:999;
                                                 box-shadow:0 4px 16px rgba(0,0,0,0.35);
                                                 width:220px;text-align:center;line-height:1.5;">
                                        A one-time platform fee that covers order processing, support, and secure payment handling.
                                    </span>
                                </span>
                            </span>
                            <span class="value" style="color:var(--text-muted);font-size:15px;">₦200.00</span>
                        </div>
                        <div class="summary-row" style="border-top:1px solid var(--border);margin-top:4px;padding-top:4px;">
                            <span class="label" style="font-weight:700;color:var(--text);">Total</span>
                            <span class="value green" id="displayRiderTotal">₦200.00</span>
                        </div>
                    </div>
                </div>

                <div class="card-block">
                    <div class="card-block-title">Payment Method</div>

                    <div class="pay-option {{ auth('web')->user()->wallet_balance > 0 ? 'selected' : '' }}"
                         id="payOptWallet" onclick="selectPayment('wallet')">
                        <div class="pay-icon">💰</div>
                        <div>
                            <div class="pay-label">Wallet Balance</div>
                            <div class="pay-sub">₦{{ number_format(auth('web')->user()->wallet_balance, 2) }} available</div>
                        </div>
                        @if(auth('web')->user()->wallet_balance > 0)
                        <span class="pay-badge">Ready</span>
                        @endif
                    </div>

                    <div class="pay-option {{ auth('web')->user()->wallet_balance <= 0 ? 'selected' : '' }}"
                         id="payOptKorapay" onclick="selectPayment('korapay')">
                        <div class="pay-icon">💳</div>
                        <div>
                            <div class="pay-label">Pay with Korapay</div>
                            <div class="pay-sub">Secure card / bank transfer in NGN</div>
                        </div>
                    </div>

                    <input type="hidden" name="payment_method" id="paymentMethod"
                           value="{{ auth('web')->user()->wallet_balance > 0 ? 'wallet' : 'korapay' }}">
                </div>

                <div class="btn-row">
                    <button type="button" class="btn-secondary" onclick="goStep(3)">
                        <svg width="14" height="14" viewBox="0 0 16 16" fill="none">
                            <path d="M10 3L5 8l5 5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        Back
                    </button>
                    <button type="submit" class="btn-primary-full">
                        <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                            <path d="M2 8h12M8 3l5 5-5 5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        Confirm & Pay
                    </button>
                </div>
            </div>

        </form>

        @else
        {{-- Guest --}}
        <div class="guest-lock">
            <div class="lock-icon">🔒</div>
            <h4>Sign in to Book a Delivery</h4>
            <p>Create an account or sign in to start booking deliveries.</p>
            <a href="{{ route('login') }}" class="btn-primary-full" style="max-width:200px;margin:0 auto;text-decoration:none;">Sign In</a>
        </div>
        @endauth

    </div>
</div>

@include('layouts.storefront.footer')

<script src="{{ asset('js/jquery/jquery-2.2.4.min.js') }}"></script>
<script src="{{ asset('js/bootstrap.min.js') }}"></script>
<script src="{{ asset('js/classy-nav.min.js') }}"></script>
<script src="{{ asset('js/active.js') }}"></script>

<script>
const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
let currentStep = 1;
const TOTAL_STEPS = 4;

// ── Step navigation ──────────────────────────────────────────────
function goStep(n) {
    // Hide current
    document.getElementById('step-panel-' + currentStep).classList.remove('active');

    // Update progress dots
    for (let i = 1; i <= TOTAL_STEPS; i++) {
        const dot = document.getElementById('pstep-' + i);
        dot.classList.remove('active', 'done');
        if (i < n)       dot.classList.add('done');
        else if (i === n) dot.classList.add('active');
    }

    // Fill connectors
    for (let i = 1; i < TOTAL_STEPS; i++) {
        const fill = document.getElementById('conn-' + i);
        fill.style.width = i < n ? '100%' : '0%';
    }

    currentStep = n;
    document.getElementById('step-panel-' + n).classList.add('active');
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

// ── Step 1: delivery type ────────────────────────────────────────
function selectDeliveryType(type) {
    document.getElementById('deliveryType').value = type;
    document.getElementById('typeLocal').classList.toggle('selected', type === 'local');
    document.getElementById('typeIntl').classList.toggle('selected', type === 'international');
}

// ── Step 1 → 2 validation ────────────────────────────────────────
// (override goStep for step 1 continue)
document.querySelector('#step-panel-1 .btn-primary-full').addEventListener('click', function(e) {
    e.stopPropagation();
    const desc = document.getElementById('itemDescription').value.trim();
    if (!desc) {
        document.getElementById('itemDescription').focus();
        document.getElementById('itemDescription').style.borderColor = 'var(--red)';
        setTimeout(() => document.getElementById('itemDescription').style.borderColor = '', 2000);
        return;
    }
    goStep(2);
}, true);

// ── Countries loader ─────────────────────────────────────────────
(async function () {
    const selects = [
        { el: document.getElementById('pickupCountry'),   def: 'NG'  },
        { el: document.getElementById('deliveryCountry'), def: null  },
    ];

    try {
        const res       = await fetch('https://restcountries.com/v3.1/all?fields=name,cca2');
        const countries = await res.json();
        countries.sort((a, b) => a.name.common.localeCompare(b.name.common));

        selects.forEach(({ el, def }) => {
            el.innerHTML = '<option value="">— Select Country —</option>';
            countries.forEach(c => {
                const o = document.createElement('option');
                o.value = c.cca2;
                o.textContent = c.name.common;
                if (c.cca2 === def) o.selected = true;
                el.appendChild(o);
            });
        });
    } catch {
        const fallback = [
            ['NG','Nigeria'],['GH','Ghana'],['KE','Kenya'],
            ['ZA','South Africa'],['US','United States'],['GB','United Kingdom']
        ];
        selects.forEach(({ el, def }) => {
            el.innerHTML = '<option value="">— Select Country —</option>';
            fallback.forEach(([val, label]) => {
                el.innerHTML += `<option value="${val}" ${val === def ? 'selected' : ''}>${label}</option>`;
            });
        });
    }
})();

// ── Step 2 → 3: validate + fetch rates ──────────────────────────
function validateAddressesAndFetchRates() {
    const required = [
        'sender_name','sender_phone','pickup_address','pickup_city','pickup_country',
        'recipient_name','recipient_phone','delivery_address','delivery_city','delivery_country'
    ];

    let firstEmpty = null;
    for (const f of required) {
        const el = document.querySelector('[name="' + f + '"]');
        if (!el || !el.value.trim()) {
            if (!firstEmpty) firstEmpty = el;
            if (el) {
                el.style.borderColor = 'var(--red)';
                setTimeout(() => el.style.borderColor = '', 2500);
            }
        }
    }

    if (firstEmpty) {
        firstEmpty.focus();
        return;
    }

    goStep(3);
    fetchRiderRates();
}

// ── Fetch rates ──────────────────────────────────────────────────
// Update your fetchRiderRates function to properly capture courier_id
function fetchRiderRates() {
    document.getElementById('riderRatesLoading').style.display = 'block';
    document.getElementById('riderRatesList').innerHTML = '';
    document.getElementById('riderFee').value = '0';
    document.getElementById('riderCourierId').value = ''; // Clear courier_id

    const body = {
        pickup_address:   document.querySelector('[name="pickup_address"]').value,
        pickup_city:      document.querySelector('[name="pickup_city"]').value,
        pickup_country:   document.querySelector('[name="pickup_country"]').value,
        delivery_address: document.querySelector('[name="delivery_address"]').value,
        delivery_city:    document.querySelector('[name="delivery_city"]').value,
        delivery_country: document.querySelector('[name="delivery_country"]').value,
        sender_name:      document.querySelector('[name="sender_name"]').value,
        sender_phone:     document.querySelector('[name="sender_phone"]').value,
        recipient_name:   document.querySelector('[name="recipient_name"]').value,
        recipient_phone:  document.querySelector('[name="recipient_phone"]').value,
        weight_kg:        document.querySelector('[name="weight_kg"]').value,
        item_description: document.querySelector('[name="item_description"]').value,
        declared_value:   document.querySelector('[name="declared_value"]').value,
    };

    fetch('{{ route("rider.rates") }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
        body: JSON.stringify(body),
    })
    .then(r => r.json())
    .then(data => {
        document.getElementById('riderRatesLoading').style.display = 'none';
        console.log('Shipbubble rates response:', data);

        if (!data.success) {
            document.getElementById('riderRatesList').innerHTML =
                `<div class="inline-alert warning">${data.message ?? 'Could not fetch rates. Please try again.'}</div>`;
            return;
        }

        const rates = data.rates;
        if (!rates || !Array.isArray(rates) || rates.length === 0) {
            document.getElementById('riderRatesList').innerHTML =
                `<div class="inline-alert warning">No rates available for this route. Try different addresses.</div>`;
            return;
        }

        let html = '';
        rates.forEach(function(rate, idx) {
            // Log each rate to see what fields are available
            console.log('Rate data:', rate);
            
            const courier     = rate.courier_name  || rate.carrier || 'Courier';
            const courierId   = rate.courier_id    || rate.carrier_id || '';
            const service     = rate.service_type  || rate.service_name || 'Standard';
            const price       = parseFloat(rate.total || rate.rate || 0).toFixed(2);
            const eta         = rate.delivery_eta || rate.eta || '';
            const serviceCode = rate.service_code || rate.code || '';
            const logoUrl     = rate.courier_image || rate.image || '';

            const logoHtml = logoUrl
                ? `<div class="rate-card-logo"><img src="${logoUrl}" alt="${courier}"></div>`
                : `<div class="rate-card-logo-fallback">🚚</div>`;

            // Store the complete rate data
            const rateDataJson = JSON.stringify(rate).replace(/'/g, "&#39;").replace(/"/g, '&quot;');
            
            html += `
            <div class="rate-card" id="rateCard_${idx}" 
                 onclick="selectRiderRate(this, '${serviceCode}', '${courierId}', '${price}', '${courier}', '${service}', '${rateDataJson}')">
                <div class="rate-card-left">
                    ${logoHtml}
                    <div>
                        <div class="rate-card-name">${courier}</div>
                        <div class="rate-card-type">${service}</div>
                        ${eta ? `<div class="rate-card-eta">⏱ ${eta}</div>` : ''}
                    </div>
                </div>
                <div class="rate-card-price">₦${price}</div>
            </div>`;
        });

        document.getElementById('riderRatesList').innerHTML = html;

        // Auto-select first rate card if available
        const firstCard = document.querySelector('.rate-card');
        if (firstCard) {
            firstCard.click();
        } else {
            console.warn('No rate cards were created');
        }
    })
    .catch(err => {
        console.error('Rates error:', err);
        document.getElementById('riderRatesLoading').style.display = 'none';
        document.getElementById('riderRatesList').innerHTML =
            `<div class="inline-alert warning">Error fetching rates: ${err.message}</div>`;
    });
}

// Update selectRiderRate function to properly set all fields
function selectRiderRate(el, serviceCode, courierId, price, carrier, service, rateDataJson) {
    console.log('Selecting rate:', { serviceCode, courierId, price, carrier, service });
    
    // Remove selected class from all rate cards
    document.querySelectorAll('.rate-card').forEach(c => c.classList.remove('selected'));
    el.classList.add('selected');

    // Set the hidden form fields
    document.getElementById('riderServiceCode').value = serviceCode || '';
    document.getElementById('riderCarrier').value = carrier || '';
    document.getElementById('riderServiceName').value = service || '';
    document.getElementById('riderFee').value = price || '0';
    document.getElementById('riderCourierId').value = courierId || '';
    
    // Parse and store rate data
    try {
        if (rateDataJson) {
            const rateData = typeof rateDataJson === 'string' ? JSON.parse(rateDataJson) : rateDataJson;
            document.getElementById('riderRateData').value = JSON.stringify(rateData);
        }
    } catch(e) {
        console.error('Error parsing rate data:', e);
        document.getElementById('riderRateData').value = rateDataJson || '{}';
    }

    // Update payment summary preview
    document.getElementById('displayRiderFee').textContent = '₦' + (parseFloat(price) || 0).toFixed(2);
    document.getElementById('displayRiderCarrier').textContent = carrier || '—';
    document.getElementById('displayRiderService').textContent = service || '—';
    const SERVICE_FEE = 200;
    const total = (parseFloat(price) || 0) + SERVICE_FEE;
    document.getElementById('displayRiderTotal').textContent = '₦' + total.toFixed(2);
 
    
    // Debug: log all hidden field values
    console.log('Hidden fields after selection:', {
        service_code: document.getElementById('riderServiceCode').value,
        courier_id: document.getElementById('riderCourierId').value,
        carrier: document.getElementById('riderCarrier').value,
        fee: document.getElementById('riderFee').value
    });
}

// Update proceedToPayment function to validate courier_id
function proceedToPayment() {
    const fee = document.getElementById('riderFee').value;
    const serviceCode = document.getElementById('riderServiceCode').value;
    const courierId = document.getElementById('riderCourierId').value;
    
    console.log('Proceeding to payment with:', { fee, serviceCode, courierId });
    
    if (!fee || parseFloat(fee) <= 0) {
        showAlert('Please select a shipping option first.', 'warning');
        return;
    }
    
    if (!serviceCode) {
        showAlert('Service code is missing. Please select a valid shipping option.', 'warning');
        return;
    }
    
    if (!courierId) {
        showAlert('Courier ID is missing. Please select a valid shipping option.', 'warning');
        return;
    }
    
    goStep(4);
}

// Helper function to show alerts
function showAlert(message, type = 'warning') {
    const alertHtml = `<div class="inline-alert ${type}" id="tempAlert">${message}</div>`;
    const ratesList = document.getElementById('riderRatesList');
    if (ratesList) {
        ratesList.insertAdjacentHTML('beforebegin', alertHtml);
        setTimeout(() => {
            const alert = document.getElementById('tempAlert');
            if (alert) alert.remove();
        }, 3000);
    } else {
        alert(message);
    }
}


// ── Payment selection ────────────────────────────────────────────
function selectPayment(method) {
    document.getElementById('paymentMethod').value = method;
    document.getElementById('payOptWallet').classList.toggle('selected', method === 'wallet');
    document.getElementById('payOptKorapay').classList.toggle('selected', method === 'korapay');
}

// ── Form submit guard ────────────────────────────────────────────
document.getElementById('riderForm').addEventListener('submit', function(e) {
    const fee = document.getElementById('riderFee').value;
    if (!fee || parseFloat(fee) <= 0) {
        e.preventDefault();
        alert('Please select a shipping option first.');
    }
});
</script>