@extends('layouts.admin')
@section('title', 'Create Flash Sale')
@section('page_title', 'Create Flash Sale')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.flash-sales.index') }}">Flash Sales</a></li>
    <li class="breadcrumb-item active">Create</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">New Flash Sale</h5>
            </div>
            <div class="card-body">

                <div class="alert alert-info mb-4">
                    <i class="feather-zap me-2"></i>
                    Flash sales appear on the homepage with a countdown timer.
                    They run for a limited time and drive urgency purchases.
                </div>

                <form action="{{ route('admin.flash-sales.store') }}" method="POST">
                    @csrf

                    <div class="mb-4">
                        <label class="form-label fw-bold">
                            Flash Sale Title <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="title" class="form-control"
                               value="{{ old('title') }}"
                               placeholder="e.g. Weekend Mega Sale" required>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">
                            Select Product <span class="text-danger">*</span>
                        </label>
                        <select name="product_id" id="productSelect" class="form-select" required>
                            <option value="">Choose a product...</option>
                            @foreach($products as $product)
                            <option value="{{ $product->id }}"
                                    data-price="{{ $product->price }}"
                                    {{ old('product_id') == $product->id ? 'selected' : '' }}>
                                {{ $product->name }}
                                ({{ $product->seller->business_name }})
                                — ₦{{ number_format($product->price, 2) }}
                            </option>
                            @endforeach
                        </select>
                        <small class="text-muted">Only approved products are shown.</small>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-bold">
                                Original Price (NGN)
                            </label>
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
                                <input type="number" name="sale_price"
                                       class="form-control @error('sale_price') is-invalid @enderror"
                                       value="{{ old('sale_price') }}"
                                       step="0.01" min="0.01"
                                       placeholder="0.00" required
                                       id="salePriceInput">
                            </div>
                            @error('sale_price')
                                <div class="text-danger fs-12 mt-1">{{ $message }}</div>
                            @enderror
                            <small class="text-success fw-semibold" id="discountPreview"></small>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Quantity Limit</label>
                        <input type="number" name="quantity_limit" class="form-control"
                               value="{{ old('quantity_limit') }}"
                               min="1" placeholder="Leave blank for unlimited">
                        <small class="text-muted">Maximum units that can be sold at flash price.</small>
                    </div>

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
                        <a href="{{ route('admin.flash-sales.index') }}"
                           class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('productSelect').addEventListener('change', function() {
    const option = this.options[this.selectedIndex];
    const price  = parseFloat(option.dataset.price || 0);
    document.getElementById('originalPriceDisplay').value = price ? '₦' + price.toFixed(2) : '';
    updateDiscount();
});

document.getElementById('salePriceInput').addEventListener('input', updateDiscount);

function updateDiscount() {
    const option = document.getElementById('productSelect').options[document.getElementById('productSelect').selectedIndex];
    const orig   = parseFloat(option?.dataset.price || 0);
    const sale   = parseFloat(document.getElementById('salePriceInput').value || 0);
    const preview = document.getElementById('discountPreview');

    if (orig > 0 && sale > 0 && sale < orig) {
        const pct = Math.round(((orig - sale) / orig) * 100);
        preview.textContent = `Buyers save ${pct}% (−₦${(orig - sale).toFixed(2)})`;
    } else {
        preview.textContent = '';
    }
}
</script>
@endpush

@endsection