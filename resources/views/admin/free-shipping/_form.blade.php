{{-- Shared form partial for create & edit --}}

<div class="row">
    <div class="col-md-8">

        {{-- Basic Info --}}
        <div class="card mb-4">
            <div class="card-header"><h6 class="card-title mb-0">Basic Info</h6></div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Rule Name <span class="text-danger">*</span></label>
                    <input type="text" name="name"
                           class="form-control @error('name') is-invalid @enderror"
                           value="{{ old('name', $freeShipping->name ?? '') }}"
                           placeholder="e.g. New Buyer Welcome Shipping" required>
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Description</label>
                    <textarea name="description" class="form-control" rows="2"
                              placeholder="Internal note about this rule">{{ old('description', $freeShipping->description ?? '') }}</textarea>
                </div>
            </div>
        </div>

        {{-- Who qualifies --}}
        <div class="card mb-4">
            <div class="card-header"><h6 class="card-title mb-0">Who Qualifies</h6></div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Applies To <span class="text-danger">*</span></label>
                    <select name="applies_to" id="appliesTo" class="form-select">
                        @foreach([
                            'all_buyers'       => 'All Buyers',
                            'new_buyers'       => 'New Buyers only',
                            'buyers_no_orders' => 'Buyers with no previous orders',
                            'specific_buyers'  => 'Specific Buyers',
                        ] as $val => $label)
                        <option value="{{ $val }}"
                            {{ old('applies_to', $freeShipping->applies_to ?? 'all_buyers') === $val ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                        @endforeach
                    </select>
                </div>

                {{-- New buyer threshold --}}
                <div id="newBuyerDaysWrap" class="mb-3" style="display:none;">
                    <label class="form-label fw-semibold">
                        "New" means registered within how many days?
                    </label>
                    <div class="input-group" style="max-width:220px;">
                        <input type="number" name="new_buyer_days" class="form-control"
                               value="{{ old('new_buyer_days', $freeShipping->new_buyer_days ?? 30) }}"
                               min="1" placeholder="30">
                        <span class="input-group-text">days</span>
                    </div>
                </div>

                {{-- Specific buyers searchable --}}
                <div id="specificBuyersWrap" style="display:none;">
                    <label class="form-label fw-semibold">Search & Select Buyers</label>
                    <select id="buyerSelect" name="buyer_ids[]" multiple
                            placeholder="Type a name or email to search…"
                            class="@error('buyer_ids') is-invalid @enderror">
                        @foreach($buyers as $buyer)
                        <option value="{{ $buyer->id }}"
                            {{ in_array($buyer->id, old('buyer_ids', isset($freeShipping) ? $freeShipping->buyers->pluck('id')->toArray() : [])) ? 'selected' : '' }}>
                            {{ $buyer->first_name }} {{ $buyer->last_name }} — {{ $buyer->email }}
                        </option>
                        @endforeach
                    </select>
                    <small class="text-muted">Search by name or email. Select as many as needed.</small>
                    @error('buyer_ids')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                </div>
            </div>
        </div>

        {{-- Product / Seller Scope --}}
        <div class="card mb-4">
            <div class="card-header"><h6 class="card-title mb-0">Product / Seller Scope</h6></div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Applies to which products?</label>
                    <select name="product_scope" id="productScope" class="form-select">
                        @foreach([
                            'all'               => 'All Products',
                            'specific_products' => 'Specific Products only',
                            'specific_sellers'  => 'Specific Sellers only',
                        ] as $val => $label)
                        <option value="{{ $val }}"
                            {{ old('product_scope', $freeShipping->product_scope ?? 'all') === $val ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                        @endforeach
                    </select>
                </div>

                {{-- Specific products searchable --}}
                <div id="specificProductsWrap" style="display:none;">
                    <label class="form-label fw-semibold">Search & Select Products</label>
                    <select id="productSelect" name="product_ids[]" multiple
                            placeholder="Type a product name to search…"
                            class="@error('product_ids') is-invalid @enderror">
                        @foreach($products as $product)
                        <option value="{{ $product->id }}"
                            {{ in_array($product->id, old('product_ids', isset($freeShipping) ? $freeShipping->products->pluck('id')->toArray() : [])) ? 'selected' : '' }}>
                            {{ $product->name }}
                        </option>
                        @endforeach
                    </select>
                    <small class="text-muted">Free shipping applies if the cart contains at least one of these products.</small>
                    @error('product_ids')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                </div>

                {{-- Specific sellers searchable --}}
                <div id="specificSellersWrap" style="display:none;">
                    <label class="form-label fw-semibold">Search & Select Sellers</label>
                    <select id="sellerSelect" name="seller_ids[]" multiple
                            placeholder="Type a business name to search…"
                            class="@error('seller_ids') is-invalid @enderror">
                        @foreach($sellers as $seller)
                        <option value="{{ $seller->id }}"
                            {{ in_array($seller->id, old('seller_ids', isset($freeShipping) ? $freeShipping->sellers->pluck('id')->toArray() : [])) ? 'selected' : '' }}>
                            {{ $seller->business_name }}
                        </option>
                        @endforeach
                    </select>
                    <small class="text-muted">Free shipping applies if the cart contains items from these sellers.</small>
                    @error('seller_ids')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                </div>
            </div>
        </div>

    </div>

    <div class="col-md-4">

        {{-- Limits --}}
        <div class="card mb-4">
            <div class="card-header"><h6 class="card-title mb-0">Limits</h6></div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Minimum Order Amount (₦)</label>
                    <div class="input-group">
                        <span class="input-group-text">₦</span>
                        <input type="number" name="minimum_order_amount" class="form-control"
                               value="{{ old('minimum_order_amount', $freeShipping->minimum_order_amount ?? '') }}"
                               min="0" step="0.01" placeholder="No minimum">
                    </div>
                    <small class="text-muted">Buyer's subtotal must reach this before rule applies.</small>
                </div>
                <div class="mb-0">
                    <label class="form-label fw-semibold">Max Shipping Discount (₦)</label>
                    <div class="input-group">
                        <span class="input-group-text">₦</span>
                        <input type="number" name="max_discount_amount" class="form-control"
                               value="{{ old('max_discount_amount', $freeShipping->max_discount_amount ?? '') }}"
                               min="0" step="0.01" placeholder="Cover full shipping">
                    </div>
                    <small class="text-muted">Leave blank to waive the full shipping fee.</small>
                </div>
            </div>
        </div>

        {{-- Schedule --}}
        <div class="card mb-4">
            <div class="card-header"><h6 class="card-title mb-0">Schedule</h6></div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Start Date & Time</label>
                    <input type="datetime-local" name="starts_at" class="form-control"
                           value="{{ old('starts_at', isset($freeShipping) && $freeShipping->starts_at
                               ? $freeShipping->starts_at->format('Y-m-d\TH:i') : '') }}">
                    <small class="text-muted">Leave blank to start immediately.</small>
                </div>
                <div class="mb-0">
                    <label class="form-label fw-semibold">End Date & Time</label>
                    <input type="datetime-local" name="ends_at" class="form-control"
                           value="{{ old('ends_at', isset($freeShipping) && $freeShipping->ends_at
                               ? $freeShipping->ends_at->format('Y-m-d\TH:i') : '') }}">
                    <small class="text-muted">Leave blank for no expiry.</small>
                </div>
            </div>
        </div>

        {{-- Status --}}
        <div class="card mb-4">
            <div class="card-header"><h6 class="card-title mb-0">Status</h6></div>
            <div class="card-body">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="is_active"
                           id="isActive" value="1"
                           {{ old('is_active', ($freeShipping->is_active ?? true) ? '1' : '0') == '1' ? 'checked' : '' }}>
                    <label class="form-check-label fw-semibold" for="isActive">
                        Rule is active
                    </label>
                </div>
                <small class="text-muted d-block mt-1">
                    Inactive rules are saved but never applied at checkout.
                </small>
            </div>
        </div>

        {{-- Summary preview --}}
        <div class="card border-success" id="ruleSummaryCard">
            <div class="card-header bg-transparent">
                <h6 class="card-title mb-0 text-success">
                    <i class="feather-eye me-1"></i> Rule Preview
                </h6>
            </div>
            <div class="card-body">
                <p class="fs-13 mb-2">
                    <span class="text-muted">Who:</span>
                    <strong id="previewAudience">—</strong>
                </p>
                <p class="fs-13 mb-2">
                    <span class="text-muted">Scope:</span>
                    <strong id="previewScope">—</strong>
                </p>
                <p class="fs-13 mb-2">
                    <span class="text-muted">Min order:</span>
                    <strong id="previewMinOrder">None</strong>
                </p>
                <p class="fs-13 mb-0">
                    <span class="text-muted">Discount:</span>
                    <strong id="previewDiscount" class="text-success">Full shipping fee</strong>
                </p>
            </div>
        </div>

    </div>
</div>

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tom-select/2.3.1/css/tom-select.bootstrap5.min.css">
<style>
.ts-wrapper.multi .ts-control {
    min-height: 42px;
    padding: 4px 8px;
    border-radius: 6px;
}
.ts-wrapper.multi .ts-control .item {
    background: #2980B9;
    color: #fff;
    border-radius: 4px;
    padding: 2px 8px;
    font-size: 12px;
    border: none;
}
.ts-wrapper.multi .ts-control .item .remove {
    color: rgba(255,255,255,0.8);
    border-left: 1px solid rgba(255,255,255,0.3);
    margin-left: 6px;
    padding-left: 5px;
}
.ts-wrapper.multi .ts-control .item .remove:hover {
    color: #fff;
    background: transparent;
}
.ts-dropdown {
    border-radius: 6px;
    box-shadow: 0 4px 16px rgba(0,0,0,0.1);
    border: 1px solid #dee2e6;
}
.ts-dropdown .option {
    padding: 8px 12px;
    font-size: 13px;
}
.ts-dropdown .option:hover,
.ts-dropdown .option.active {
    background: #f0faf5;
    color: #1a1a2e;
}
.ts-dropdown .highlight {
    background: #d5f5e3;
    border-radius: 2px;
}
.ts-wrapper .ts-control input::placeholder {
    color: #adb5bd;
    font-size: 13px;
}
</style>
@endpush

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/tom-select/2.3.1/js/tom-select.complete.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {

    // ── Tom Select instances ─────────────────────────────────────
    let buyerTS   = null;
    let productTS = null;
    let sellerTS  = null;

    function initBuyerSelect() {
        if (buyerTS) return;
        buyerTS = new TomSelect('#buyerSelect', {
            plugins: ['remove_button', 'clear_button'],
            maxOptions: 200,
            searchField: ['text'],
            placeholder: 'Type a name or email to search…',
            render: {
                option: function(data, escape) {
                    const parts = escape(data.text).split(' — ');
                    const name  = parts[0] ?? '';
                    const email = parts[1] ?? '';
                    return `<div class="d-flex flex-column py-1">
                        <span class="fw-semibold fs-13">${name}</span>
                        <small class="text-muted">${email}</small>
                    </div>`;
                },
                item: function(data, escape) {
                    const name = escape(data.text).split(' — ')[0];
                    return `<div title="${escape(data.text)}">${name}</div>`;
                },
            },
        });
    }

    function initProductSelect() {
        if (productTS) return;
        productTS = new TomSelect('#productSelect', {
            plugins: ['remove_button', 'clear_button'],
            maxOptions: 300,
            searchField: ['text'],
            placeholder: 'Type a product name to search…',
            render: {
                option: function(data, escape) {
                    return `<div class="py-1 fs-13">${escape(data.text)}</div>`;
                },
                item: function(data, escape) {
                    return `<div title="${escape(data.text)}">${escape(data.text)}</div>`;
                },
            },
        });
    }

    function initSellerSelect() {
        if (sellerTS) return;
        sellerTS = new TomSelect('#sellerSelect', {
            plugins: ['remove_button', 'clear_button'],
            maxOptions: 200,
            searchField: ['text'],
            placeholder: 'Type a business name to search…',
            render: {
                option: function(data, escape) {
                    return `<div class="py-1 fs-13">${escape(data.text)}</div>`;
                },
                item: function(data, escape) {
                    return `<div title="${escape(data.text)}">${escape(data.text)}</div>`;
                },
            },
        });
    }

    // ── Show/hide logic ──────────────────────────────────────────
    const appliesTo    = document.getElementById('appliesTo');
    const productScope = document.getElementById('productScope');

    const audienceLabels = {
        all_buyers:       'All Buyers',
        new_buyers:       'New Buyers',
        buyers_no_orders: 'Buyers With No Orders',
        specific_buyers:  'Specific Buyers',
    };
    const scopeLabels = {
        all:               'All Products',
        specific_products: 'Specific Products',
        specific_sellers:  'Specific Sellers',
    };

    function toggleAppliesTo() {
        const v = appliesTo.value;

        document.getElementById('newBuyerDaysWrap').style.display   = v === 'new_buyers'      ? 'block' : 'none';
        document.getElementById('specificBuyersWrap').style.display = v === 'specific_buyers' ? 'block' : 'none';

        if (v === 'specific_buyers') {
            initBuyerSelect();
        }

        document.getElementById('previewAudience').textContent = audienceLabels[v] ?? v;
    }

    function toggleProductScope() {
        const v = productScope.value;

        document.getElementById('specificProductsWrap').style.display = v === 'specific_products' ? 'block' : 'none';
        document.getElementById('specificSellersWrap').style.display  = v === 'specific_sellers'  ? 'block' : 'none';

        if (v === 'specific_products') initProductSelect();
        if (v === 'specific_sellers')  initSellerSelect();

        document.getElementById('previewScope').textContent = scopeLabels[v] ?? v;
    }

    function updatePreview() {
        const minOrder = document.querySelector('[name="minimum_order_amount"]').value;
        const maxDisc  = document.querySelector('[name="max_discount_amount"]').value;

        document.getElementById('previewMinOrder').textContent =
            minOrder ? '₦' + parseFloat(minOrder).toLocaleString('en-NG', {minimumFractionDigits: 2}) : 'None';

        document.getElementById('previewDiscount').textContent =
            maxDisc  ? 'Up to ₦' + parseFloat(maxDisc).toLocaleString('en-NG', {minimumFractionDigits: 2}) : 'Full shipping fee';
    }

    appliesTo.addEventListener('change', toggleAppliesTo);
    productScope.addEventListener('change', toggleProductScope);

    document.querySelector('[name="minimum_order_amount"]').addEventListener('input', updatePreview);
    document.querySelector('[name="max_discount_amount"]').addEventListener('input', updatePreview);

    // Run on load to restore state after validation errors or on edit page
    toggleAppliesTo();
    toggleProductScope();
    updatePreview();
});
</script>
@endpush