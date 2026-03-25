@extends('layouts.seller')
@section('title', 'New Ticket')
@section('page_title', 'Open Support Ticket')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('seller.support') }}">Support</a></li>
    <li class="breadcrumb-item active">New Ticket</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">New Support Ticket</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('seller.support.store') }}" method="POST">
                    @csrf

                    <div class="mb-4">
                        <label class="form-label fw-bold">Subject <span class="text-danger">*</span></label>
                        <input type="text" name="subject" class="form-control"
                               value="{{ old('subject') }}"
                               placeholder="Brief description of your issue" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-bold">Category <span class="text-danger">*</span></label>
                            <select name="category" class="form-select" required>
                                <option value="">Select category</option>
                                <option value="order_issue"  {{ old('category') === 'order_issue'  ? 'selected' : '' }}>Order Issue</option>
                                <option value="payment"      {{ old('category') === 'payment'      ? 'selected' : '' }}>Payment</option>
                                <option value="account"      {{ old('category') === 'account'      ? 'selected' : '' }}>Account</option>
                                <option value="product"      {{ old('category') === 'product'      ? 'selected' : '' }}>Product</option>
                                <option value="shipping"     {{ old('category') === 'shipping'     ? 'selected' : '' }}>Shipping</option>
                                <option value="other"        {{ old('category') === 'other'        ? 'selected' : '' }}>Other</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-bold">Priority <span class="text-danger">*</span></label>
                            <select name="priority" class="form-select" required>
                                <option value="low"    {{ old('priority') === 'low'    ? 'selected' : '' }}>Low</option>
                                <option value="medium" {{ old('priority','medium') === 'medium' ? 'selected' : '' }}>Medium</option>
                                <option value="high"   {{ old('priority') === 'high'   ? 'selected' : '' }}>High</option>
                                <option value="urgent" {{ old('priority') === 'urgent' ? 'selected' : '' }}>Urgent</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Message <span class="text-danger">*</span></label>
                        <textarea name="message" class="form-control" rows="6"
                                  placeholder="Describe your issue in detail..."
                                  required>{{ old('message') }}</textarea>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="feather-send me-2"></i> Submit Ticket
                        </button>
                        <a href="{{ route('seller.support') }}" class="btn btn-outline-secondary">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection