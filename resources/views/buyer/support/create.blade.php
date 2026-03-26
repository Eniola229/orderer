@extends('layouts.buyer')
@section('title', 'New Ticket')
@section('page_title', 'Open Support Ticket')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('buyer.support') }}">Support</a></li>
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
                <form action="{{ route('buyer.support.store') }}" method="POST">
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
                                <option value="order_issue">Order Issue</option>
                                <option value="payment">Payment</option>
                                <option value="account">Account</option>
                                <option value="product">Product</option>
                                <option value="shipping">Shipping</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-bold">Priority <span class="text-danger">*</span></label>
                            <select name="priority" class="form-select" required>
                                <option value="low">Low</option>
                                <option value="medium" selected>Medium</option>
                                <option value="high">High</option>
                                <option value="urgent">Urgent</option>
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
                        <a href="{{ route('buyer.support') }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
