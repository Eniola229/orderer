@extends('layouts.seller')
@section('title', 'Request Withdrawal')
@section('page_title', 'Request Withdrawal')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('seller.withdrawals.index') }}">Withdrawals</a></li>
    <li class="breadcrumb-item active">New Request</li>
@endsection

@section('content')

<div class="row">

    {{-- Left --}}
    <div class="col-lg-8">

        {{-- Balance card --}}
        <div class="card mb-3">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted fs-12 mb-1">Available to withdraw</p>
                        <p class="fw-bold text-success mb-0" style="font-size:32px;">
                            ${{ number_format($wallet->balance, 2) }}
                        </p>
                    </div>
                    <div class="rounded-circle d-flex align-items-center justify-content-center"
                         style="width:56px;height:56px;background:#D5F5E3;">
                        <i class="feather-dollar-sign text-success" style="font-size:24px;"></i>
                    </div>
                </div>
            </div>
        </div>

        <form action="{{ route('seller.withdrawals.store') }}" method="POST">
            @csrf

            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="card-title mb-0">Withdrawal Details</h5>
                </div>
                <div class="card-body">

                    <div class="mb-4">
                        <label class="form-label fw-bold">
                            Amount (USD) <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number"
                                   name="amount"
                                   class="form-control @error('amount') is-invalid @enderror"
                                   value="{{ old('amount') }}"
                                   step="0.01"
                                   min="10"
                                   max="{{ $wallet->balance }}"
                                   placeholder="Minimum $10.00"
                                   required>
                            @error('amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">
                            Bank Name <span class="text-danger">*</span>
                        </label>
                        <input type="text"
                               name="bank_name"
                               class="form-control @error('bank_name') is-invalid @enderror"
                               value="{{ old('bank_name') }}"
                               placeholder="e.g. First Bank, Guaranty Trust Bank"
                               required>
                        @error('bank_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-bold">
                                Account Number <span class="text-danger">*</span>
                            </label>
                            <input type="text"
                                   name="account_number"
                                   class="form-control @error('account_number') is-invalid @enderror"
                                   value="{{ old('account_number') }}"
                                   placeholder="Account number"
                                   required>
                            @error('account_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-bold">
                                Account Name <span class="text-danger">*</span>
                            </label>
                            <input type="text"
                                   name="account_name"
                                   class="form-control @error('account_name') is-invalid @enderror"
                                   value="{{ old('account_name') }}"
                                   placeholder="Name on account"
                                   required>
                            @error('account_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-bold">
                                Bank Country <span class="text-danger">*</span>
                            </label>
                            <select name="bank_country" class="form-select" required>
                                <option value="NG" {{ old('bank_country','NG') === 'NG' ? 'selected' : '' }}>Nigeria (NG)</option>
                                <option value="GH" {{ old('bank_country') === 'GH' ? 'selected' : '' }}>Ghana (GH)</option>
                                <option value="KE" {{ old('bank_country') === 'KE' ? 'selected' : '' }}>Kenya (KE)</option>
                                <option value="ZA" {{ old('bank_country') === 'ZA' ? 'selected' : '' }}>South Africa (ZA)</option>
                                <option value="US" {{ old('bank_country') === 'US' ? 'selected' : '' }}>United States (US)</option>
                                <option value="GB" {{ old('bank_country') === 'GB' ? 'selected' : '' }}>United Kingdom (GB)</option>
                                <option value="OTHER" {{ old('bank_country') === 'OTHER' ? 'selected' : '' }}>Other</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-bold">SWIFT / BIC Code</label>
                            <input type="text"
                                   name="swift_code"
                                   class="form-control"
                                   value="{{ old('swift_code') }}"
                                   placeholder="For international transfers">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">
                            Can this account receive USD? <span class="text-danger">*</span>
                        </label>
                        <div class="d-flex gap-4 mt-1">
                            <label class="d-flex align-items-center gap-2 fs-13 fw-normal text-transform-none cursor-pointer">
                                <input type="radio" name="dollar_capable" value="yes"
                                       {{ old('dollar_capable','yes') === 'yes' ? 'checked' : '' }}>
                                Yes — it accepts USD
                            </label>
                            <label class="d-flex align-items-center gap-2 fs-13 fw-normal cursor-pointer">
                                <input type="radio" name="dollar_capable" value="no"
                                       {{ old('dollar_capable') === 'no' ? 'checked' : '' }}>
                                No — local currency only
                            </label>
                        </div>
                    </div>

                </div>
            </div>

        </form>

    </div>

    {{-- Right --}}
    <div class="col-lg-4">

        <div class="card mb-3">
            <div class="card-header">
                <h5 class="card-title mb-0">Submit</h5>
            </div>
            <div class="card-body">
                <div class="alert alert-warning mb-3">
                    <i class="feather-info me-2"></i>
                    All withdrawals are processed in <strong>USD</strong>. Processing takes 1–3 business days.
                </div>
                <div class="d-grid gap-2">
                    <button type="submit" form="withdrawalForm" class="btn btn-primary">
                        <i class="feather-send me-2"></i> Submit Request
                    </button>
                    <a href="{{ route('seller.withdrawals.index') }}"
                       class="btn btn-outline-secondary">
                        Cancel
                    </a>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Important Notes</h5>
            </div>
            <div class="card-body">
                <ul class="list-unstyled fs-13 text-muted">
                    <li class="mb-2">
                        <i class="feather-check-circle text-success me-2"></i>
                        Minimum withdrawal is <strong>$10.00</strong>
                    </li>
                    <li class="mb-2">
                        <i class="feather-check-circle text-success me-2"></i>
                        Ensure account can receive USD transfers
                    </li>
                    <li class="mb-2">
                        <i class="feather-check-circle text-success me-2"></i>
                        Double-check your account number
                    </li>
                    <li class="mb-0">
                        <i class="feather-check-circle text-success me-2"></i>
                        Processing takes 1–3 business days
                    </li>
                </ul>
            </div>
        </div>

    </div>
</div>

@push('scripts')
<script>
// Wire the form submit to the button outside the form tag
document.querySelector('[form="withdrawalForm"]').addEventListener('click', function() {
    document.getElementById('withdrawalForm') && document.getElementById('withdrawalForm').submit();
});
</script>
@endpush

@endsection