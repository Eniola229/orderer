@extends('layouts.seller')
@section('title', 'Create Flash Sale')
@section('page_title', 'Create Flash Sale')
@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{ route('seller.flash-sales.index') }}">Flash Sales</a>
    </li>
    <li class="breadcrumb-item active">Create</li>
@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .select2-container--default .select2-selection--single {
        height: calc(1.5em + 0.75rem + 2px);
        padding: 0.375rem 0.75rem;
        font-size: 1rem;
        border: 1px solid #dee2e6;
        border-radius: 0.375rem;
        display: flex;
        align-items: center;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 1.5; padding: 0; color: #212529;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 100%; top: 0; right: 8px;
    }
    .select2-container--default .select2-search--dropdown .select2-search__field {
        border: 1px solid #dee2e6; border-radius: 0.25rem;
        padding: 0.375rem 0.75rem; font-size: 0.95rem;
    }
    .select2-container--default .select2-results__option--highlighted[aria-selected] {
        background-color: #0d6efd;
    }
    .select2-dropdown {
        border: 1px solid #dee2e6; border-radius: 0.375rem;
        box-shadow: 0 4px 16px rgba(0,0,0,0.10);
    }
    .select2-container { width: 100% !important; }

    .product-option-name   { font-weight: 600; font-size: 0.95rem; }
    .product-option-price  { font-size: 0.85rem; color: #198754; font-weight: 600; float: right; }
</style>
@endpush

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-7">

        {{-- Info banner --}}
        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="feather-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        @if($products->isEmpty())
        <div class="alert alert-warning">
            <i class="feather-alert-triangle me-2"></i>
            You don't have any <strong>approved</strong> products yet.
            Flash sales can only be run on approved listings.
        </div>
        @else
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="feather-zap me-2 text-warning"></i> New Flash Sale
                </h5>
            </div>
            <div class="card-body">

                <div class="alert alert-info mb-4">
                    <i class="feather-info me-2"></i>
                    Flash sales appear on the homepage with a countdown timer and drive urgency purchases.
                    Set a discounted price and a time window — the rest is automatic.
                </div>

                <form action="{{ route('seller.flash-sales.store') }}" method="POST">
                    @csrf

                    {{-- Title --}}
                    <div class="mb-4">
                        <label class="form-label fw-bold">
                            Flash Sale Title <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
                               value="{{ old('title') }}"
                               placeholder="e.g. Weekend Mega Sale" required>
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Product picker --}}
                    <div class="mb-4">
                        <label class="form-label fw-bold">
                            Select Product <span class="text-danger">*</span>
                        </label>
                        <select name="product_id" id="productSelect" class="form-select" required>
                            <option value="">Search for a product…</option>
                            @foreach($products as $product)
                            <option value="{{ $product->id }}"
                                    data-price="{{ $product->price }}"
                                    {{ old('product_id') == $product->id ? 'selected' : '' }}>
                                {{ $product->name }}
                            </option>
                            @endforeach
                        </select>
                        @error('product_id')
                            <div class="text-danger fs-12 mt-1">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Only your approved products are listed.</small>
                    </div>

                    {{-- Prices --}}
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-bold">Original Price (NGN)</label>
                            <div class="input-group">
                                <span class="input-group-text">₦</span>
                                <input type="text" id="originalPriceDisplay"
                                       class="form-control" disabled
                                       placeholder="Select product first"
                                       style="background:#f5f5f5;">
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-bold">
                                Flash Sale Price (NGN) <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">₦</span>
                                <input type="number" name="sale_price" id="salePriceInput"
                                       class="form-control @error('sale_price') is-invalid @enderror"
                                       value="{{ old('sale_price') }}"
                                       step="0.01" min="0.01"
                                       placeholder="0.00" required>
                            </div>
                            @error('sale_price')
                                <div class="text-danger fs-12 mt-1">{{ $message }}</div>
                            @enderror
                            <small class="text-success fw-semibold" id="discountPreview"></small>
                        </div>
                    </div>

                    {{-- Quantity limit --}}
                    <div class="mb-4">
                        <label class="form-label fw-bold">Quantity Limit</label>
                        <input type="number" name="quantity_limit" class="form-control"
                               value="{{ old('quantity_limit') }}"
                               min="1" placeholder="Leave blank for unlimited">
                        <small class="text-muted">Max units sold at the flash price. Leave blank for unlimited.</small>
                    </div>

                    {{-- Dates --}}
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-bold">
                                Start Date &amp; Time <span class="text-danger">*</span>
                            </label>
                            <input type="datetime-local" name="starts_at"
                                   class="form-control"
                                   value="{{ old('starts_at', now()->format('Y-m-d\TH:i')) }}"
                                   required>
                        </div>
                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-bold">
                                End Date &amp; Time <span class="text-danger">*</span>
                            </label>
                            <input type="datetime-local" name="ends_at"
                                   class="form-control"
                                   value="{{ old('ends_at', now()->addDays(2)->format('Y-m-d\TH:i')) }}"
                                   required>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="feather-zap me-2"></i> Create Flash Sale
                        </button>
                        <a href="{{ route('seller.flash-sales.index') }}"
                           class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
        @endif

    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
$(function () {

    function formatProductOption(option) {
        if (!option.id) return option.text;
        const price = parseFloat($(option.element).data('price') || 0);
        return $(
            `<div>
                <span class="product-option-price">
                    ₦${price.toLocaleString('en-NG', {minimumFractionDigits: 2})}
                </span>
                <span class="product-option-name">${option.text}</span>
            </div>`
        );
    }

    $('#productSelect').select2({
        placeholder: 'Type to search your products…',
        allowClear: true,
        templateResult: formatProductOption,
        templateSelection: function(option) {
            if (!option.id) return option.text;
            const price = parseFloat($(option.element).data('price') || 0);
            return `${option.text} — ₦${price.toLocaleString('en-NG', {minimumFractionDigits: 2})}`;
        }
    });

    $('#productSelect').on('change', function () {
        const selected = $(this).find(':selected');
        const price    = parseFloat(selected.data('price') || 0);
        $('#originalPriceDisplay').val(
            price ? price.toLocaleString('en-NG', {minimumFractionDigits: 2}) : ''
        );
        updateDiscount();
    });

    $('#salePriceInput').on('input', updateDiscount);

    function updateDiscount() {
        const orig    = parseFloat($('#productSelect').find(':selected').data('price') || 0);
        const sale    = parseFloat($('#salePriceInput').val() || 0);
        const preview = $('#discountPreview');

        if (orig > 0 && sale > 0 && sale < orig) {
            const pct   = Math.round(((orig - sale) / orig) * 100);
            const saved = (orig - sale).toLocaleString('en-NG', {minimumFractionDigits: 2});
            preview.text(`Buyers save ${pct}% (−₦${saved})`);
        } else {
            preview.text('');
        }
    }

    // Restore on old() values
    if ($('#productSelect').val()) {
        $('#productSelect').trigger('change');
    }
});
</script>
@endpush