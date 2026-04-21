{{--
    resources/views/seller/products/_product_options_swatches.blade.php

    Drop this block into the storefront show.blade.php
    RIGHT ABOVE the "Add to cart" form, inside .single_product_desc

    Requires: $product->options loaded with values
--}}

@if($product->options->count())
<div class="product-options mb-3" id="productOptions">
    @foreach($product->options as $option)
    <div class="mb-3" data-option-id="{{ $option->id }}">
        <p class="mb-2" style="font-size:14px;font-weight:700;color:#1a1a1a;">
            {{ $option->name }}:
            <span class="selected-label text-muted fw-normal"
                  id="selected-label-{{ $option->id }}">— please select</span>
        </p>
        <div class="d-flex flex-wrap gap-2">
            @foreach($option->values as $val)
            <button type="button"
                    class="option-swatch"
                    data-option-id="{{ $option->id }}"
                    data-option-name="{{ $option->name }}"
                    data-value-id="{{ $val->id }}"
                    data-value="{{ $val->value }}"
                    data-image="{{ $val->image_url ?? '' }}"
                    title="{{ $val->value }}"
                    style="
                        border: 2px solid #ddd;
                        border-radius: 8px;
                        background: #fff;
                        cursor: pointer;
                        padding: 0;
                        overflow: hidden;
                        transition: border-color .15s, transform .15s;
                        {{ $val->image_url ? 'width:52px;height:52px;' : 'padding:6px 14px;height:36px;font-size:13px;font-weight:600;' }}
                    ">
                @if($val->image_url)
                    <img src="{{ $val->image_url }}"
                         style="width:100%;height:100%;object-fit:cover;display:block;"
                         alt="{{ $val->value }}">
                @else
                    {{ $val->value }}
                @endif
            </button>
            @endforeach
        </div>
        {{-- Inline validation message --}}
        <small class="option-error text-danger mt-1"
               id="option-error-{{ $option->id }}"
               style="display:none;">
            Please select a {{ $option->name }}.
        </small>
    </div>
    @endforeach
</div>

<style>
.option-swatch:hover {
    border-color: #2ECC71 !important;
    transform: translateY(-1px);
}
.option-swatch.active {
    border-color: #2ECC71 !important;
    box-shadow: 0 0 0 2px rgba(46,204,113,.35);
    transform: translateY(-1px);
}
</style>

<script>
(function () {
    // selectedOptions keyed by option_id
    // { "uuid": { option_id, option_name, value_id, value, image_url } }
    window._selectedOptions = {};

    document.querySelectorAll('.option-swatch').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const optionId   = this.dataset.optionId;
            const optionName = this.dataset.optionName;
            const valueId    = this.dataset.valueId;
            const value      = this.dataset.value;
            const imgUrl     = this.dataset.image;

            // Deselect siblings
            document.querySelectorAll(`.option-swatch[data-option-id="${optionId}"]`)
                    .forEach(b => b.classList.remove('active'));
            this.classList.add('active');

            // Update label
            const label = document.getElementById('selected-label-' + optionId);
            if (label) label.textContent = value;

            // Hide error
            const err = document.getElementById('option-error-' + optionId);
            if (err) err.style.display = 'none';

            // Store selection
            window._selectedOptions[optionId] = {
                option_id:   optionId,
                option_name: optionName,
                value_id:    valueId,
                value:       value,
                image_url:   imgUrl || null,
            };

            // Swap main product image if swatch has one
            if (imgUrl) {
                const mainImg = document.getElementById('mainProductImg');
                if (mainImg) {
                    mainImg.src = imgUrl;
                    document.querySelectorAll('.thumb-img').forEach(function (t) {
                        t.style.borderColor = (t.src === imgUrl) ? '#2ECC71' : '#eee';
                    });
                }
            }
        });
    });

    /**
     * Called by addToCart() before submitting.
     * Returns false and shows inline errors if any option is missing.
     */
    window.validateOptions = function () {
        let valid = true;
        document.querySelectorAll('#productOptions [data-option-id]').forEach(function (group) {
            const optionId = group.dataset.optionId;
            if (!window._selectedOptions[optionId]) {
                const err = document.getElementById('option-error-' + optionId);
                if (err) err.style.display = 'block';
                valid = false;
            }
        });
        return valid;
    };

    /**
     * Returns the selected options as an array ready for the cart payload.
     */
    window.getSelectedOptions = function () {
        return Object.values(window._selectedOptions);
    };
})();
</script>
@endif