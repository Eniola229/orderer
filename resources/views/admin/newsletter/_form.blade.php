{{-- Shared form partial: used by create.blade.php and edit.blade.php --}}

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

<div class="mb-3">
    <label class="form-label fw-semibold">Audience <span class="text-danger">*</span></label>
    <div class="d-flex gap-4 mt-1">
         @foreach([
            'buyers'  => 'Buyers only',
            'sellers' => 'Sellers only',
            'both'    => 'Buyers & Sellers',
            'guests'  => 'Subscribers only',
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

<div class="mb-3">
    <label class="form-label fw-semibold">Body <span class="text-danger">*</span></label>
    <textarea id="newsletter-body"
              name="body"
              class="form-control @error('body') is-invalid @enderror"
              rows="16">{{ old('body', $newsletter->body ?? '') }}</textarea>
    @error('body')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
    <small class="text-muted">Use the editor to format your content. Use <code>&#123;&#123; $recipientName &#125;&#125;</code> to personalise.</small>
</div>
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
@endpush