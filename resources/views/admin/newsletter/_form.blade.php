{{-- Shared form partial: used by create.blade.php and edit.blade.php --}}

{{-- ── Subject ── --}}
<div class="mb-3">
    <label class="form-label fw-semibold">Subject <span class="text-danger">*</span></label>
    <input type="text"
           name="subject"
           class="form-control @error('subject') is-invalid @enderror"
           value="{{ old('subject', $newsletter->subject ?? '') }}"
           placeholder="e.g. Exciting news from Orderer 🎉"
           required />
    @error('subject')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

{{-- ── Email Audience ── --}}
<div class="mb-3">
    <label class="form-label fw-semibold">Email Audience <span class="text-danger">*</span></label>
    <div class="d-flex flex-wrap gap-3 mt-1">
        @foreach([
            'buyers'              => 'Buyers only',
            'sellers'             => 'Sellers only',
            'both'                => 'Buyers & Sellers',
            'guests'              => 'Subscribers only',
            'new_buyers'          => 'New Buyers (last 30 days)',
            'buyers_no_orders'    => 'Buyers – No Orders Yet',
            'buyers_with_orders'  => 'Buyers – Have Ordered',
            'sellers_no_listings' => 'Sellers – No Listings Yet',
        ] as $val => $label)
        <div class="form-check">
            <input class="form-check-input" type="radio" name="audience"
                   id="aud_{{ $val }}" value="{{ $val }}"
                   {{ old('audience', $newsletter->audience ?? 'both') === $val ? 'checked' : '' }} />
            <label class="form-check-label" for="aud_{{ $val }}">{{ $label }}</label>
        </div>
        @endforeach
    </div>
    @error('audience')
        <div class="text-danger small mt-1">{{ $message }}</div>
    @enderror
</div>

{{-- ── Body (TinyMCE) ── --}}
<div class="mb-3">
    <label class="form-label fw-semibold">Body <span class="text-danger">*</span></label>
    <textarea id="newsletter-body"
              name="body"
              class="form-control @error('body') is-invalid @enderror"
              rows="16">{{ old('body', $newsletter->body ?? '') }}</textarea>
    @error('body')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
    <small class="text-muted">
        Use the editor to format your content.
        Use <code>&#123;&#123; $recipientName &#125;&#125;</code> to personalise.
    </small>
</div>

{{-- ══════════════════════════════════════════════════════════════════════════
     SMS SECTION — completely separate from email above
═══════════════════════════════════════════════════════════════════════════ --}}
<hr class="my-4">
<div class="mb-1">
    <h6 class="fw-semibold text-muted text-uppercase fs-11 mb-3">
        <i class="feather-message-square me-1"></i> SMS (Optional)
    </h6>
</div>

<div class="mb-3">
    <div class="form-check form-switch">
        <input class="form-check-input" type="checkbox" role="switch"
               name="send_sms" id="sendSmsToggle" value="1"
               @checked(old('send_sms', $newsletter->send_sms ?? false))>
        <label class="form-check-label fw-semibold" for="sendSmsToggle">
            Also send as SMS
        </label>
    </div>
    <small class="text-muted">
        SMS is sent independently from the email above — you can target a different audience.
    </small>
</div>

<div id="smsSection" style="{{ old('send_sms', $newsletter->send_sms ?? false) ? '' : 'display:none;' }}">
    <div class="card border-0 bg-light p-3 mb-3">

        {{-- SMS Audience --}}
        <div class="mb-3">
            <label class="form-label fw-semibold">
                SMS Audience <span class="text-danger">*</span>
            </label>
            <small class="text-muted d-block mb-2">
                Independent from the email audience selected above.
            </small>
            <div class="d-flex flex-wrap gap-3">
                @php $smAud = old('sms_audience', $newsletter->sms_audience ?? ''); @endphp
                <div class="form-check">
                    <input class="form-check-input" type="radio"
                           name="sms_audience" id="sms_aud_users" value="users"
                           {{ $smAud === 'users' ? 'checked' : '' }}>
                    <label class="form-check-label" for="sms_aud_users">Users (Buyers)</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio"
                           name="sms_audience" id="sms_aud_sellers" value="sellers"
                           {{ $smAud === 'sellers' ? 'checked' : '' }}>
                    <label class="form-check-label" for="sms_aud_sellers">Sellers</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio"
                           name="sms_audience" id="sms_aud_both" value="both"
                           {{ $smAud === 'both' ? 'checked' : '' }}>
                    <label class="form-check-label" for="sms_aud_both">Both Users & Sellers</label>
                </div>
            </div>
            @error('sms_audience')
                <div class="text-danger small mt-1">{{ $message }}</div>
            @enderror
        </div>

        {{-- SMS Message --}}
        <div class="mb-3">
            <label class="form-label fw-semibold">
                SMS Message <span class="text-danger">*</span>
                <span class="text-muted fw-normal fs-12">— plain text only, no HTML</span>
            </label>
            <textarea name="sms_message" id="smsMessage"
                      class="form-control @error('sms_message') is-invalid @enderror"
                      rows="3" maxlength="320"
                      placeholder="Keep it concise. 160 chars = 1 SMS credit.">{{ old('sms_message', $newsletter->sms_message ?? '') }}</textarea>
            <div class="d-flex justify-content-between mt-1">
                <small class="text-muted">
                    Special characters like <code>{ } [ ] ~ €</code> reduce limit to 70 chars/SMS.
                </small>
                <small id="smsCharCount" class="text-muted">0 / 160</small>
            </div>
            @error('sms_message')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Extra Numbers --}}
        <div class="mb-2">
            <label class="form-label fw-semibold">
                Extra Numbers
                <span class="text-muted fw-normal fs-12">
                    — send to specific numbers in addition to the audience above
                    (international format, no +, e.g. <code>2348012345678</code>)
                </span>
            </label>
            <div id="extraNumbersContainer">
                @php
                    $extraNumbers = old('sms_extra_numbers', $newsletter->sms_extra_numbers ?? []);
                    if (empty($extraNumbers)) $extraNumbers = [''];
                @endphp
                @foreach($extraNumbers as $idx => $num)
                <div class="input-group mb-2 extra-number-row">
                    <input type="text"
                           name="sms_extra_numbers[]"
                           class="form-control"
                           placeholder="e.g. 2348012345678"
                           value="{{ $num }}">
                    <button type="button"
                            class="btn btn-outline-danger btn-remove-number"
                            {{ $idx === 0 ? 'style=display:none' : '' }}>
                        <i class="feather-minus"></i>
                    </button>
                </div>
                @endforeach
            </div>
            <button type="button" class="btn btn-sm btn-outline-secondary mt-1" id="btnAddNumber">
                <i class="feather-plus me-1"></i> Add another number
            </button>
        </div>

    </div>
</div>

{{-- ══ Scripts ══════════════════════════════════════════════════════════════ --}}
@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.8.3/tinymce.min.js"></script>
<script>
  tinymce.init({
    selector: '#newsletter-body',
    height: 400,
    menubar: true,
    plugins: ['advlist','autolink','lists','link','image','charmap','preview',
      'anchor','searchreplace','visualblocks','code','fullscreen',
      'insertdatetime','media','table','help','wordcount'],
    toolbar: 'undo redo | blocks | bold italic backcolor | alignleft aligncenter ' +
      'alignright alignjustify | bullist numlist outdent indent | removeformat | help',
    content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:16px; line-height:1.6; }',
    branding: false
  });
</script>
<script>
(function () {
    // ── Toggle SMS section ────────────────────────────────────────────────────
    const toggle  = document.getElementById('sendSmsToggle');
    const section = document.getElementById('smsSection');

    toggle.addEventListener('change', function () {
        section.style.display = this.checked ? '' : 'none';
    });

    // ── Character counter ─────────────────────────────────────────────────────
    const smsArea  = document.getElementById('smsMessage');
    const smsCount = document.getElementById('smsCharCount');

    function updateCount() {
        const len = smsArea.value.length;
        smsCount.textContent = len + ' / 160';
        smsCount.className   = len > 160 ? 'text-danger fw-semibold' : 'text-muted';
    }

    smsArea.addEventListener('input', updateCount);
    updateCount();

    // ── Dynamic extra numbers ─────────────────────────────────────────────────
    const container = document.getElementById('extraNumbersContainer');
    const addBtn    = document.getElementById('btnAddNumber');

    addBtn.addEventListener('click', function () {
        const row = document.createElement('div');
        row.className = 'input-group mb-2 extra-number-row';
        row.innerHTML =
            '<input type="text" name="sms_extra_numbers[]" class="form-control"' +
            ' placeholder="e.g. 2348012345678">' +
            '<button type="button" class="btn btn-outline-danger btn-remove-number">' +
            '<i class="feather-minus"></i></button>';
        container.appendChild(row);
        syncRemoveButtons();
    });

    container.addEventListener('click', function (e) {
        if (e.target.closest('.btn-remove-number')) {
            e.target.closest('.extra-number-row').remove();
            syncRemoveButtons();
        }
    });

    function syncRemoveButtons() {
        const rows = container.querySelectorAll('.extra-number-row');
        rows.forEach(function (row) {
            row.querySelector('.btn-remove-number').style.display =
                rows.length === 1 ? 'none' : '';
        });
    }
})();
</script>
@endpush