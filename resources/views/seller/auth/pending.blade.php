@extends('layouts.auth')
@section('title', 'Account Status')

@section('content')
@php
    $seller = auth('seller')->user();
    $sellerDocument = $seller->document()->first();
    $isRejected = $seller->verification_status === 'rejected';
    $isApproved = $seller->verification_status === 'approved' && $seller->is_approved;
    
    // Redirect to dashboard if approved
    if ($isApproved) {
        header('Location: ' . route('seller.dashboard'));
        exit;
    }
@endphp
<div style="min-height:100vh;display:flex;align-items:center;justify-content:center;padding:40px 20px;background:#f8f9fa;">
    <div style="max-width:620px;width:100%;">

        <img src="{{ asset('dashboard/assets/images/favicon.png') }}"
             style="height:40px;margin-bottom:32px;display:block;margin-left:auto;margin-right:auto;" alt="Orderer">

        @if($isRejected)
            {{-- Rejected Account View --}}
            <div class="text-center mb-4">
                <div class="avatar-text avatar-xl rounded mx-auto mb-4"
                     style="width:80px;height:80px;background:#FEE2E2;color:#DC2626;font-size:32px;">
                    <i class="feather-alert-circle"></i>
                </div>

                <h3 class="fw-bold mb-3" style="color:#DC2626;">Account Not Approved</h3>

                <div class="alert alert-danger mb-4" style="background:#FEF2F2;border-color:#FEE2E2;">
                    <i class="feather-info me-2"></i>
                    <strong>Reason:</strong> {{ $seller->rejection_reason ?? 'No specific reason provided.' }}
                </div>

                <p class="text-muted mb-4">
                    Please review the feedback above, update your information, and resubmit your application.
                </p>
            </div>

            {{-- Edit Form --}}
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Update Your Application</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('seller.resubmit') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">First Name *</label>
                                <input type="text" name="first_name" class="form-control @error('first_name') is-invalid @enderror" 
                                       value="{{ old('first_name', $seller->first_name) }}" required>
                                @error('first_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Last Name *</label>
                                <input type="text" name="last_name" class="form-control @error('last_name') is-invalid @enderror" 
                                       value="{{ old('last_name', $seller->last_name) }}" required>
                                @error('last_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Email *</label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" 
                                   value="{{ old('email', $seller->email) }}" required>
                            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Phone *</label>
                            <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" 
                                   value="{{ old('phone', $seller->phone) }}" required>
                            @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Business Name *</label>
                            <input type="text" name="business_name" class="form-control @error('business_name') is-invalid @enderror" 
                                   value="{{ old('business_name', $seller->business_name) }}" required>
                            @error('business_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Business Address</label>
                            <textarea name="business_address" class="form-control @error('business_address') is-invalid @enderror" rows="2">{{ old('business_address', $seller->business_address) }}</textarea>
                            @error('business_address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Address Code</label>
                            <input type="text" name="address_code" class="form-control @error('address_code') is-invalid @enderror" 
                                   value="{{ old('address_code', $seller->address_code) }}">
                            @error('address_code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">New Password (optional)</label>
                            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" 
                                   placeholder="Leave blank to keep current password">
                            @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Confirm Password</label>
                            <input type="password" name="password_confirmation" class="form-control">
                        </div>

                        {{-- Document Section - Always show if rejected, regardless of is_verified_business --}}
                        <div class="mb-3">
                            <div class="form-check mb-2">
                                <input type="checkbox" name="upload_document" value="1" class="form-check-input" id="upload_document" 
                                       {{ $sellerDocument ? 'checked' : '' }}>
                                <label class="form-check-label" for="upload_document">
                                    I want to upload/submit business verification documents
                                </label>
                            </div>
                            
                            <div id="documentSection" style="{{ $sellerDocument ? 'display: block;' : 'display: none;' }}">
                                <label class="form-label">Document Type *</label>
                                <select name="document_type" class="form-control @error('document_type') is-invalid @enderror">
                                    <option value="">Select Document Type</option>
                                    <option value="government_id" {{ old('document_type', $sellerDocument?->document_type) == 'government_id' ? 'selected' : '' }}>Government-issued ID</option>
                                    <option value="cac_certificate" {{ old('document_type', $sellerDocument?->document_type) == 'cac_certificate' ? 'selected' : '' }}>CAC Certificate</option>
                                    <option value="tax_id" {{ old('document_type', $sellerDocument?->document_type) == 'tax_id' ? 'selected' : '' }}>Tax ID</option>
                                    <option value="business_license" {{ old('document_type', $sellerDocument?->document_type) == 'business_license' ? 'selected' : '' }}>Business License</option>
                                    <option value="utility_bill" {{ old('document_type', $sellerDocument?->document_type) == 'utility_bill' ? 'selected' : '' }}>Utility Bill</option>
                                </select>
                                @error('document_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                
                                <div class="mt-3">
                                    <label class="form-label">Upload Document</label>
                                    <input type="file" name="document_file" class="form-control @error('document_file') is-invalid @enderror" 
                                           accept=".pdf,.jpg,.jpeg,.png">
                                    <small class="text-muted">Max 5MB. PDF, JPG, JPEG, PNG allowed.</small>
                                    
                                    @if($sellerDocument && $sellerDocument->document_url)
                                        <div class="mt-2 p-2 bg-light rounded">
                                            <small>Current document: 
                                                <a href="{{ $sellerDocument->document_url }}" target="_blank">
                                                    {{ $sellerDocument->original_filename ?? 'View Document' }}
                                                </a>
                                                @if($sellerDocument->status === 'rejected')
                                                    <span class="text-danger">(Previously rejected)</span>
                                                @endif
                                            </small>
                                            @if($sellerDocument->rejection_reason)
                                                <div class="text-danger mt-1">
                                                    <small>Document rejection reason: {{ $sellerDocument->rejection_reason }}</small>
                                                </div>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </div>
                            @error('document_file')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input type="checkbox" name="terms" class="form-check-input" id="terms" required {{ old('terms') ? 'checked' : '' }}>
                                <label class="form-check-label" for="terms">
                                    I confirm that the information provided is accurate and agree to the <a href="{{ route('legal.terms') }}" target="_blank">Terms of Service</a>
                                </label>
                            </div>
                        </div>

                        <div class="d-flex gap-3">
                            <button type="submit" class="btn btn-primary">
                                <i class="feather-save me-1"></i> Resubmit Application
                            </button>
                            <a href="#" class="btn btn-outline-secondary" 
                               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="feather-log-out me-1"></i> Sign Out
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        @elseif($isApproved)
            {{-- This should never show because of redirect above, but kept as fallback --}}
            <div class="text-center">
                <div class="avatar-text avatar-xl rounded mx-auto mb-4"
                     style="width:80px;height:80px;background:#D5F5E3;color:#2ECC71;font-size:32px;">
                    <i class="feather-check-circle"></i>
                </div>
                <h3 class="fw-bold mb-3">Account Approved!</h3>
                <p class="text-muted mb-4">Redirecting you to your dashboard...</p>
                <a href="{{ route('seller.dashboard') }}" class="btn btn-primary">Go to Dashboard</a>
            </div>
        @else
            {{-- Pending Review View --}}
            <div class="text-center">
                <div class="avatar-text avatar-xl rounded mx-auto mb-4"
                     style="width:80px;height:80px;background:#D5F5E3;color:#2ECC71;font-size:32px;">
                    <i class="feather-clock"></i>
                </div>

                <h3 class="fw-bold mb-3">Account under review</h3>

                <p class="text-muted mb-4" style="line-height:1.8;">
                    Thank you for registering as a seller on Orderer.
                    Our team is reviewing your details
                    @if($sellerDocument)
                        and the documents you submitted
                    @endif
                    . This usually takes <strong>within 24 hours</strong>.
                </p>

                <div class="card mb-4 text-start">
                    <div class="card-body">
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <i class="feather-check-circle text-success" style="font-size:20px;"></i>
                            <span class="fw-semibold">Account created</span>
                        </div>
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <i class="feather-clock text-warning" style="font-size:20px;"></i>
                            <span class="fw-semibold text-muted">Application under review</span>
                        </div>
                        <div class="d-flex align-items-center gap-3">
                            <i class="feather-file-text text-info" style="font-size:20px;"></i>
                            <span class="text-muted">Document status: 
                                @if($sellerDocument && $sellerDocument->status === 'pending')
                                    <span class="text-warning">Under review</span>
                                @elseif($sellerDocument && $sellerDocument->status === 'approved')
                                    <span class="text-success">Approved</span>
                                @elseif($sellerDocument && $sellerDocument->status === 'rejected')
                                    <span class="text-danger">Rejected</span>
                                @else
                                    <span class="text-muted">Not uploaded</span>
                                @endif
                            </span>
                        </div>
                    </div>
                </div>

                <p class="text-muted fs-13 mb-4">
                    We'll email <strong>{{ $seller->email }}</strong> when approved.
                </p>
            </div>
        @endif

        <div class="d-flex gap-3 justify-content-center mt-4">
            @if(!$isRejected && !$isApproved)
                <form id="logout-form" action="{{ route('seller.logout') }}" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn btn-outline-secondary">
                        <i class="feather-log-out me-1"></i> Sign Out
                    </button>
                </form>
            @endif
            <a href="{{ route('contact') }}" class="btn btn-outline-primary">
                <i class="feather-headphones me-1"></i> Contact Support
            </a>
        </div>

    </div>
</div>

<script>
    document.getElementById('upload_document').addEventListener('change', function() {
        const documentSection = document.getElementById('documentSection');
        if (this.checked) {
            documentSection.style.display = 'block';
            document.querySelector('select[name="document_type"]').required = true;
            document.querySelector('input[name="document_file"]').required = true;
        } else {
            documentSection.style.display = 'none';
            document.querySelector('select[name="document_type"]').required = false;
            document.querySelector('input[name="document_file"]').required = false;
        }
    });
</script>
@endsection