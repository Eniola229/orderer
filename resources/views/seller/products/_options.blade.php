{{--
    resources/views/seller/products/_options.blade.php
--}}

@php $existingOptions = collect($existingOptions ?? []); @endphp

<div class="card mb-3" id="optionsCard">
    <div class="card-header d-flex align-items-center justify-content-between">
        <div>
            <h5 class="card-title mb-0">Product Options</h5>
            <small class="text-muted">Optional — e.g. Size, Color, Material</small>
        </div>
        <button type="button" class="btn btn-outline-primary btn-sm" id="addOptionBtn">
            <i class="feather-plus me-1"></i> Add Option
        </button>
    </div>
    <div class="card-body">

        <div id="optionsList">

            {{-- Render existing options (edit mode) --}}
            @foreach($existingOptions as $optIdx => $opt)
            <div class="option-group border rounded p-3 mb-3" data-option-index="{{ $optIdx }}">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div style="flex:1;" class="me-3">
                        <label class="form-label fw-bold mb-1" style="font-size:13px;">
                            Option Name <span class="text-danger">*</span>
                        </label>
                        <input type="text"
                               name="options[{{ $optIdx }}][name]"
                               class="form-control form-control-sm"
                               value="{{ $opt->name }}"
                               placeholder="e.g. Color, Size, Material">
                    </div>
                    <button type="button"
                            class="btn btn-sm btn-outline-danger remove-option-btn"
                            style="margin-top:22px;">
                        <i class="feather-trash-2"></i>
                    </button>
                </div>

                <label class="form-label fw-bold mb-2" style="font-size:13px;">Values</label>
                <div class="option-values-list mb-2">
                    @foreach($opt->values as $valIdx => $val)
                    <div class="option-value-row d-flex align-items-center gap-2 mb-2"
                         data-value-index="{{ $valIdx }}">
                        <input type="text"
                               name="options[{{ $optIdx }}][values][{{ $valIdx }}][value]"
                               class="form-control form-control-sm"
                               value="{{ $val->value }}"
                               placeholder="e.g. Red"
                               style="max-width:180px;">

                        @if($val->image_url)
                        <div class="position-relative" style="width:40px;height:40px;flex-shrink:0;">
                            <img src="{{ $val->image_url }}"
                                 style="width:40px;height:40px;object-fit:cover;border-radius:6px;border:1px solid #ddd;"
                                 alt="">
                            <span style="position:absolute;bottom:-6px;left:50%;transform:translateX(-50%);font-size:9px;color:#888;white-space:nowrap;">current</span>
                        </div>
                        @endif

                        <label class="btn btn-sm btn-outline-secondary mb-0" style="cursor:pointer;white-space:nowrap;">
                            <i class="feather-image me-1" style="font-size:11px;"></i>
                            {{ $val->image_url ? 'Change' : 'Image' }}
                            <input type="file"
                                   name="options[{{ $optIdx }}][values][{{ $valIdx }}][image]"
                                   accept="image/jpg,image/jpeg,image/png,image/webp"
                                   style="display:none;"
                                   onchange="previewOptionValueImage(this)">
                        </label>
                        <img class="option-img-preview" src=""
                             style="width:36px;height:36px;object-fit:cover;border-radius:6px;border:1px solid #ddd;display:none;" alt="">

                        <button type="button" class="btn btn-sm btn-outline-danger remove-value-btn">
                            <i class="feather-x" style="font-size:11px;"></i>
                        </button>
                    </div>
                    @endforeach
                </div>

                <button type="button" class="btn btn-sm btn-outline-secondary add-value-btn"
                        data-option-index="{{ $optIdx }}">
                    <i class="feather-plus me-1" style="font-size:11px;"></i> Add Value
                </button>
            </div>
            @endforeach

        </div>

        <p id="noOptionsMsg" class="text-muted fs-13 text-center py-3 mb-0"
           style="{{ $existingOptions->count() ? 'display:none;' : '' }}">
            No options added yet. Click <strong>Add Option</strong> to add sizes, colors, etc.
        </p>
    </div>
</div>

<script>
(function () {
    let optionCount = {{ $existingOptions->count() }};

    function optionGroupHtml(optIdx) {
        return `
        <div class="option-group border rounded p-3 mb-3" data-option-index="${optIdx}">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div style="flex:1;" class="me-3">
                    <label class="form-label fw-bold mb-1" style="font-size:13px;">
                        Option Name <span class="text-danger">*</span>
                    </label>
                    <input type="text"
                           name="options[${optIdx}][name]"
                           class="form-control form-control-sm"
                           placeholder="e.g. Color, Size, Material">
                </div>
                <button type="button"
                        class="btn btn-sm btn-outline-danger remove-option-btn"
                        style="margin-top:22px;">
                    <i class="feather-trash-2"></i>
                </button>
            </div>
            <label class="form-label fw-bold mb-2" style="font-size:13px;">Values</label>
            <div class="option-values-list mb-2">
                ${valueRowHtml(optIdx, 0)}
            </div>
            <button type="button" class="btn btn-sm btn-outline-secondary add-value-btn"
                    data-option-index="${optIdx}">
                <i class="feather-plus me-1" style="font-size:11px;"></i> Add Value
            </button>
        </div>`;
    }

    function valueRowHtml(optIdx, valIdx) {
        return `
        <div class="option-value-row d-flex align-items-center gap-2 mb-2" data-value-index="${valIdx}">
            <input type="text"
                   name="options[${optIdx}][values][${valIdx}][value]"
                   class="form-control form-control-sm"
                   placeholder="e.g. Red"
                   style="max-width:180px;">
            <label class="btn btn-sm btn-outline-secondary mb-0" style="cursor:pointer;white-space:nowrap;">
                <i class="feather-image me-1" style="font-size:11px;"></i> Image
                <input type="file"
                       name="options[${optIdx}][values][${valIdx}][image]"
                       accept="image/jpg,image/jpeg,image/png,image/webp"
                       style="display:none;"
                       onchange="previewOptionValueImage(this)">
            </label>
            <img class="option-img-preview" src=""
                 style="width:36px;height:36px;object-fit:cover;border-radius:6px;border:1px solid #ddd;display:none;" alt="">
            <button type="button" class="btn btn-sm btn-outline-danger remove-value-btn">
                <i class="feather-x" style="font-size:11px;"></i>
            </button>
        </div>`;
    }

    document.getElementById('addOptionBtn').addEventListener('click', function () {
        const list = document.getElementById('optionsList');
        const div  = document.createElement('div');
        div.innerHTML = optionGroupHtml(optionCount);
        list.appendChild(div.firstElementChild);
        optionCount++;
        toggleNoOptionsMsg();
    });

    document.getElementById('optionsList').addEventListener('click', function (e) {

        const removeOptionBtn = e.target.closest('.remove-option-btn');
        if (removeOptionBtn) {
            removeOptionBtn.closest('.option-group').remove();
            toggleNoOptionsMsg();
            return;
        }

        const addValueBtn = e.target.closest('.add-value-btn');
        if (addValueBtn) {
            const optIdx   = addValueBtn.dataset.optionIndex;
            const optGroup = addValueBtn.closest('.option-group');
            const valList  = optGroup.querySelector('.option-values-list');
            const valCount = valList.querySelectorAll('.option-value-row').length;
            const div      = document.createElement('div');
            div.innerHTML  = valueRowHtml(optIdx, valCount);
            valList.appendChild(div.firstElementChild);
            return;
        }

        const removeValueBtn = e.target.closest('.remove-value-btn');
        if (removeValueBtn) {
            const row     = removeValueBtn.closest('.option-value-row');
            const valList = row.closest('.option-values-list');
            if (valList.querySelectorAll('.option-value-row').length > 1) {
                row.remove();
            } else {
                row.querySelector('input[type="text"]').value = '';
                const preview = row.querySelector('.option-img-preview');
                if (preview) { preview.src = ''; preview.style.display = 'none'; }
                const fileInput = row.querySelector('input[type="file"]');
                if (fileInput) fileInput.value = '';
            }
            return;
        }
    });

    function toggleNoOptionsMsg() {
        const groups = document.querySelectorAll('#optionsList .option-group');
        document.getElementById('noOptionsMsg').style.display = groups.length === 0 ? '' : 'none';
    }
})();

function previewOptionValueImage(input) {
    const row     = input.closest('.option-value-row');
    const preview = row.querySelector('.option-img-preview');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function (e) {
            preview.src = e.target.result;
            preview.style.display = 'inline-block';
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>