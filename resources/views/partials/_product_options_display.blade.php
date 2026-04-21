{{--
    resources/views/partials/_product_options_display.blade.php

    Drop this card AFTER the Images card in:
      - resources/views/seller/products/show.blade.php
      - resources/views/admin/products/show.blade.php

    Requires: $product->options loaded with values
    (add 'options.values' to your load() call in the controller)
--}}

@if($product->options->count())
<div class="card mb-3">
    <div class="card-header d-flex align-items-center justify-content-between">
        <h5 class="card-title mb-0">Product Options</h5>
        <span class="badge bg-secondary">{{ $product->options->count() }} option{{ $product->options->count() > 1 ? 's' : '' }}</span>
    </div>
    <div class="card-body">
        @foreach($product->options as $option)
        <div class="{{ !$loop->last ? 'mb-4 pb-4 border-bottom' : '' }}">
            <p class="fw-bold mb-2" style="font-size:13px;">
                {{ $option->name }}
                <span class="text-muted fw-normal">({{ $option->values->count() }} value{{ $option->values->count() > 1 ? 's' : '' }})</span>
            </p>
            <div class="d-flex flex-wrap gap-2">
                @foreach($option->values as $val)
                <div style="
                    display: inline-flex;
                    align-items: center;
                    gap: 6px;
                    border: 1px solid #e5e7eb;
                    border-radius: 8px;
                    padding: {{ $val->image_url ? '4px 10px 4px 4px' : '5px 12px' }};
                    background: #f9fafb;
                    font-size: 13px;
                    font-weight: 600;
                ">
                    @if($val->image_url)
                    <img src="{{ $val->image_url }}"
                         style="width:28px;height:28px;border-radius:6px;object-fit:cover;cursor:pointer;"
                         onclick="openImageModal('{{ $val->image_url }}')"
                         alt="{{ $val->value }}"
                         title="Click to enlarge">
                    @endif
                    <span>{{ $val->value }}</span>
                </div>
                @endforeach
            </div>
        </div>
        @endforeach
    </div>
</div>
@endif