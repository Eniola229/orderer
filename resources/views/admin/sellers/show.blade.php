@extends('layouts.admin')
@section('title', 'Seller: ' . $seller->business_name)
@section('page_title', $seller->business_name)
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.sellers.index') }}">Sellers</a></li>
    <li class="breadcrumb-item active">{{ $seller->business_name }}</li>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-4">

        {{-- Profile --}}
        <div class="card mb-3">
            <div class="card-body text-center">
                @if($seller->avatar)
                <img src="{{ $seller->avatar }}"
                     style="width:80px;height:80px;border-radius:50%;object-fit:cover;border:3px solid #2ECC71;margin-bottom:12px;" alt="">
                @else
                <div style="width:80px;height:80px;border-radius:50%;background:#2ECC71;color:#fff;display:flex;align-items:center;justify-content:center;font-size:28px;font-weight:700;margin:0 auto 12px;">
                    {{ strtoupper(substr($seller->first_name,0,1)) }}
                </div>
                @endif
                <h5 class="fw-bold">{{ $seller->business_name }}</h5>
                <p class="text-muted fs-13">{{ $seller->full_name }}</p>
                <p class="text-muted fs-13"><i class="feather-mail me-1"></i> {{ $seller->email }}</p>
                <p class="text-muted fs-13"><i class="feather-phone me-1"></i> {{ $seller->phone ?? 'Not provided' }}</p>
                <p class="text-muted fs-13"><i class="feather-map-pin me-1"></i> {{ $seller->business_address ?? 'Not provided' }}</p>
                <p class="text-muted fs-13"><i class="feather-file-text me-1"></i> {{ $seller->business_description ?? 'No description' }}</p>
                
                <div class="d-flex gap-2 justify-content-center flex-wrap mb-3">
                    @if($seller->verification_status === 'rejected')
                        <span class="badge orderer-badge badge-rejected">
                            <i class="feather-x-circle me-1"></i> Account Rejected
                        </span>
                    @else
                        <span class="badge orderer-badge {{ $seller->is_approved ? 'badge-approved' : 'badge-pending' }}">
                            {{ $seller->is_approved ? 'Approved' : 'Pending' }}
                        </span>
                    @endif
                    
                    @if($seller->is_verified_business)
                        <span class="badge orderer-badge badge-verified">
                            <i class="feather-check-circle me-1"></i> Verified Business
                        </span>
                    @endif
                    
                    @if($seller->document && $seller->document->status === 'rejected')
                        <span class="badge orderer-badge badge-document-rejected">
                            <i class="feather-file-text me-1"></i> Document Rejected
                        </span>
                    @endif
                    
                    @if($seller->is_active == 0)
                        <span class="badge orderer-badge badge-suspended">
                            <i class="feather-alert-circle me-1"></i> Suspended
                        </span>
                    @endif
                </div>

                @if($seller->verification_status === 'rejected' && $seller->rejection_reason)
                    <div class="alert alert-danger mt-3">
                        <i class="feather-alert-triangle me-2"></i>
                        <strong>Account Rejection Reason:</strong> {{ $seller->rejection_reason }}
                    </div>
                @endif

                @if($seller->document && $seller->document->status === 'rejected' && $seller->document->rejection_reason)
                    <div class="alert alert-warning mt-2">
                        <i class="feather-file-text me-2"></i>
                        <strong>Document Rejection Reason:</strong> {{ $seller->document->rejection_reason }}
                    </div>
                @endif
                
                <div class="border rounded p-3 text-start">
                    <div class="d-flex justify-content-between mb-2">
                        <small class="text-muted">Total Orders</small>
                        <strong>{{ $orderCount }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <small class="text-muted">Total Earnings</small>
                        <strong class="text-success">₦{{ number_format($totalEarnings, 2) }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <small class="text-muted">Wallet Balance</small>
                        <strong class="text-primary">₦{{ number_format($wallet->balance ?? 0, 2) }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <small class="text-muted">Ads Balance</small>
                        <strong class="text-info">₦{{ number_format($wallet->ads_balance ?? 0, 2) }}</strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <small class="text-muted">Joined</small>
                        <strong>{{ $seller->created_at->format('M d, Y') }}</strong>
                    </div>
                </div>
            </div>
        </div>

        {{-- Seller Stats --}}
        <div class="card mb-3">
            <div class="card-header">
                <h5 class="card-title mb-0">Seller Statistics</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-6">
                        <div class="p-2 bg-light rounded text-center">
                            <small class="text-muted d-block">Products</small>
                            <strong class="h5 mb-0">{{ $seller->products->count() }}</strong>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-2 bg-light rounded text-center">
                            <small class="text-muted d-block">Total Sales</small>
                            <strong class="h5 mb-0">{{ number_format($totalEarnings ?? 0, 2) }}</strong>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-2 bg-light rounded text-center">
                            <small class="text-muted d-block">Avg Rating</small>
                            <strong class="h5 mb-0">
                                @if($seller->brand)
                                    {{ number_format($seller->brand->average_rating ?? 0, 1) }} ★
                                @else
                                    0 ★
                                @endif
                            </strong>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-2 bg-light rounded text-center">
                            <small class="text-muted d-block">Reviews</small>
                            <strong class="h5 mb-0">{{ $seller->brand->total_reviews ?? 0 }}</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Wallet adjustment -- finance only --}}
        @if(auth('admin')->user()->canManageFinance())
        <div class="card mb-3">
            <div class="card-header">
                <h5 class="card-title mb-0">Wallet Adjustment</h5>
            </div>
            <div class="card-body">
                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <div class="p-2 bg-light rounded text-center">
                            <small class="text-muted d-block">Balance</small>
                            <strong class="text-success">₦{{ number_format($wallet->balance ?? 0, 2) }}</strong>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-2 bg-light rounded text-center">
                            <small class="text-muted d-block">Ads Balance</small>
                            <strong class="text-primary">₦{{ number_format($wallet->ads_balance ?? 0, 2) }}</strong>
                        </div>
                    </div>
                </div>
                <form action="{{ route('admin.sellers.wallet', $seller->id) }}" method="POST">
                    @csrf @method('PUT')
                    <div class="mb-2">
                        <label class="form-label fw-bold fs-13">Wallet</label>
                        <select name="wallet_type" class="form-select form-select-sm">
                            <option value="balance">Main Wallet</option>
                            <option value="ads">Ads Balance</option>
                        </select>
                    </div>
                    <div class="mb-2">
                        <label class="form-label fw-bold fs-13">Type</label>
                        <select name="type" class="form-select form-select-sm">
                            <option value="credit">Credit</option>
                            <option value="debit">Debit</option>
                        </select>
                    </div>
                    <div class="mb-2">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">₦</span>
                            <input type="number" name="amount" class="form-control"
                                   min="0.01" step="0.01" placeholder="Amount" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <input type="text" name="reason" class="form-control form-control-sm"
                               placeholder="Reason (required)" required>
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm w-100"
                            onclick="return confirm('Apply wallet adjustment?')">
                        Apply
                    </button>
                </form>
            </div>
        </div>
        @endif

        {{-- Approval actions --}}
        @if(auth('admin')->user()->canModerateSellers())
        <div class="card">
            <div class="card-header"><h5 class="card-title mb-0">Actions</h5></div>
            <div class="card-body d-grid gap-2">
                @if(!$seller->is_approved)
                <form action="{{ route('admin.sellers.approve', $seller->id) }}" method="POST">
                    @csrf @method('PUT')
                    <button type="submit" class="btn btn-success w-100">
                        <i class="feather-check me-2"></i> Approve Seller
                    </button>
                </form>
                <button type="button" class="btn btn-outline-danger"
                        onclick="openRejectModal('{{ $seller->id }}', '{{ addslashes($seller->business_name) }}')">
                    <i class="feather-x me-2"></i> Reject Application
                </button>
                @elseif($seller->is_active == 0)
                <form action="{{ route('admin.sellers.unsuspend', $seller->id) }}" method="POST">
                    @csrf @method('PUT')
                    <button type="submit" class="btn btn-success w-100">
                        <i class="feather-user-check me-2"></i> Reinstate
                    </button>
                </form>
                @else
                <button type="button" class="btn btn-outline-danger"
                        onclick="openSuspendModal('{{ $seller->id }}', '{{ addslashes($seller->business_name) }}')">
                    <i class="feather-user-x me-2"></i> Suspend
                </button>
                @endif
            </div>
        </div>
        @endif

    </div>

    <div class="col-lg-8">

        {{-- Documents --}}
        @if($seller->documents->count())
        <div class="card mb-3">
            <div class="card-header">
                <h5 class="card-title mb-0">Submitted Documents</h5>
            </div>
            <div class="card-body">
                @foreach($seller->documents as $doc)
                <div class="d-flex align-items-center gap-3 mb-3 p-3 bg-light rounded">
                    <i class="feather-file-text text-primary" style="font-size:24px;"></i>
                    <div class="flex-grow-1">
                        <p class="mb-0 fw-semibold fs-13">
                            {{ ucfirst(str_replace('_', ' ', $doc->document_type)) }}
                        </p>
                        @if($doc->status)
                        <small class="text-muted">Status: 
                            <span class="badge {{ $doc->status === 'approved' ? 'bg-success' : ($doc->status === 'rejected' ? 'bg-danger' : 'bg-warning') }}">
                                {{ ucfirst($doc->status) }}
                            </span>
                        </small>
                        @endif
                    </div>
                    <a href="{{ $doc->document_url }}" target="_blank"
                       class="btn btn-sm btn-outline-primary">
                        <i class="feather-external-link me-1"></i> View
                    </a>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Products --}}
        <div class="card mb-3">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0">
                    Products ({{ $seller->products->count() }})
                </h5>
                <a href="{{ route('admin.products.index', ['search' => $seller->business_name]) }}"
                   class="btn btn-sm btn-outline-primary">All Products</a>
            </div>
            <div class="card-body p-0">
                @if($seller->products->count())
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                             <tr>
                                <th class="fs-11 text-uppercase text-muted fw-semibold">Product</th>
                                <th class="fs-11 text-uppercase text-muted fw-semibold">Price</th>
                                <th class="fs-11 text-uppercase text-muted fw-semibold">Stock</th>
                                <th class="fs-11 text-uppercase text-muted fw-semibold">Status</th>
                             </tr>
                        </thead>
                        <tbody>
                            @foreach($seller->products->take(6) as $product)
                              <tr>
                                <td class="fw-semibold fs-13">{{ Str::limit($product->name, 40) }}</td>
                                <td class="fw-bold">₦{{ number_format($product->price, 2) }}</td>
                                <td>{{ $product->stock }}</td>
                                <td>
                                    <span class="badge orderer-badge badge-{{ $product->status }}">
                                        {{ ucfirst($product->status) }}
                                    </span>
                                </td>
                              </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-4 text-muted">No products yet.</div>
                @endif
            </div>
        </div>

        {{-- Wallet Transactions --}}
        <div class="card mb-3">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0">
                    <i class="feather-activity me-2"></i> Recent Wallet Transactions
                </h5>
                @if($wallet)
                <a href="{{ route('admin.finance.transactions', ['search' => $seller->email]) }}" 
                   class="btn btn-sm btn-outline-primary">
                    View All
                </a>
                @endif
            </div>
            <div class="card-body p-0">
                @if($transactions && count($transactions) > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                              <tr>
                                <th class="fs-11 text-uppercase text-muted fw-semibold">Reference</th>
                                <th class="fs-11 text-uppercase text-muted fw-semibold">Type</th>
                                <th class="fs-11 text-uppercase text-muted fw-semibold">Amount</th>
                                <th class="fs-11 text-uppercase text-muted fw-semibold">Balance After</th>
                                <th class="fs-11 text-uppercase text-muted fw-semibold">Status</th>
                                <th class="fs-11 text-uppercase text-muted fw-semibold">Date</th>
                              </tr>
                        </thead>
                        <tbody>
                            @foreach($transactions as $txn)
                            @php 
                                $isCredit = in_array($txn->type, ['credit','escrow_release','referral_credit','escrow_refund']);
                                $statusColors = [
                                    'completed' => '#28a745',
                                    'pending' => '#ffc107',
                                    'failed' => '#dc3545',
                                    'reversed' => '#6c757d',
                                ];
                                $statusColor = $statusColors[$txn->status] ?? '#6c757d';
                            @endphp
                              <tr>
                                <td>
                                    <code class="fs-12">{{ Str::limit($txn->reference, 20) }}</code>
                                </td>
                                <td>
                                    <span class="badge orderer-badge {{ $isCredit ? 'badge-approved' : 'badge-pending' }}">
                                        {{ str_replace('_', ' ', ucfirst($txn->type)) }}
                                    </span>
                                </td>
                                <td class="fw-bold {{ $isCredit ? 'text-success' : 'text-danger' }}">
                                    {{ $isCredit ? '+' : '-' }}₦{{ number_format($txn->amount, 2) }}
                                </td>
                                <td class="fw-semibold">₦{{ number_format($txn->balance_after, 2) }}</td>
                                <td>
                                    <span class="badge" style="
                                        background-color: {{ $statusColor }};
                                        color: {{ $txn->status === 'pending' ? '#212529' : '#ffffff' }};
                                        padding: 5px 10px;
                                        border-radius: 4px;
                                        font-size: 11px;
                                        font-weight: 600;
                                    ">
                                        {{ ucfirst($txn->status) }}
                                    </span>
                                </td>
                                <td class="text-muted fs-12">
                                    {{ $txn->created_at->format('M d, Y H:i') }}
                                </td>
                              </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-4 text-muted">
                    <i class="feather-activity mb-2 d-block" style="font-size:32px;"></i>
                    <p>No wallet transactions yet.</p>
                </div>
                @endif
            </div>
        </div>

        {{-- Brand --}}
        @php
            $brand = $seller->brand;
        @endphp
        @if($brand)
        <div class="card">
            <div class="card-header"><h5 class="card-title mb-0">Brand Information</h5></div>
            <div class="card-body">
                <div class="d-flex align-items-center gap-3 mb-3">
                    @if($brand->logo)
                    <img src="{{ $brand->logo }}"
                         style="width:80px;height:80px;object-fit:contain;border-radius:8px;background:#f5f5f5;padding:8px;" alt="">
                    @endif
                    <div>
                        <p class="mb-0 fw-bold h5">{{ $brand->name }}</p>
                        <p class="text-muted fs-13">{{ $brand->description ?? 'No description' }}</p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-6">
                        <div class="text-center p-2 bg-light rounded">
                            <small class="text-muted d-block">Rating</small>
                            <div class="text-warning">
                                @for($i=1;$i<=5;$i++) 
                                    {{ $i<=round($brand->average_rating ?? 0)?'★':'☆' }} 
                                @endfor
                                <small class="text-muted">({{ number_format($brand->average_rating ?? 0, 1) }})</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="text-center p-2 bg-light rounded">
                            <small class="text-muted d-block">Total Reviews</small>
                            <strong class="h6 mb-0">{{ number_format($brand->total_reviews ?? 0) }}</strong>
                        </div>
                    </div>
                </div>
                <div class="mt-3 text-center">
                    <a href="{{ route('brands.show', $brand->slug) }}" target="_blank"
                       class="btn btn-sm btn-outline-primary">
                        <i class="feather-external-link me-1"></i> View Brand Page
                    </a>
                </div>
            </div>
        </div>
        @endif

    </div>
</div>

{{-- Custom Reject Modal --}}
<div id="rejectModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); z-index: 999999; align-items: center; justify-content: center;">
    <div style="background: white; border-radius: 12px; max-width: 500px; width: 90%; margin: auto; box-shadow: 0 10px 40px rgba(0,0,0,0.2); animation: modalFadeIn 0.3s ease;">
        <div style="padding: 20px; border-bottom: 1px solid #e5e7eb;">
            <h5 style="margin: 0; font-size: 18px; font-weight: 600;">Reject Seller Application</h5>
        </div>
        <form id="rejectForm" method="POST" action="">
            @csrf
            @method('PUT')
            <div style="padding: 20px;">
                <p id="modalSellerInfo" style="margin-bottom: 20px; color: #6b7280; font-size: 14px;"></p>
                <div style="margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 8px; font-weight: 600; font-size: 14px;">Reason</label>
                    <textarea name="reason" rows="4" style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px;" placeholder="Why are you rejecting this seller?" required></textarea>
                </div>
            </div>
            <div style="padding: 20px; border-top: 1px solid #e5e7eb; display: flex; gap: 10px; justify-content: flex-end;">
                <button type="button" onclick="closeRejectModal()" style="padding: 8px 20px; background: #f3f4f6; border: none; border-radius: 6px; cursor: pointer; font-size: 14px;">Cancel</button>
                <button type="submit" style="padding: 8px 20px; background: #dc2626; color: white; border: none; border-radius: 6px; cursor: pointer; font-size: 14px;">Reject</button>
            </div>
        </form>
    </div>
</div>

{{-- Custom Suspend Modal --}}
<div id="suspendModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); z-index: 999999; align-items: center; justify-content: center;">
    <div style="background: white; border-radius: 12px; max-width: 500px; width: 90%; margin: auto; box-shadow: 0 10px 40px rgba(0,0,0,0.2); animation: modalFadeIn 0.3s ease;">
        <div style="padding: 20px; border-bottom: 1px solid #e5e7eb;">
            <h5 style="margin: 0; font-size: 18px; font-weight: 600;">Suspend Seller</h5>
        </div>
        <form id="suspendForm" method="POST" action="">
            @csrf
            @method('PUT')
            <div style="padding: 20px;">
                <p id="suspendSellerInfo" style="margin-bottom: 20px; color: #6b7280; font-size: 14px;"></p>
                <div style="margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 8px; font-weight: 600; font-size: 14px;">Reason</label>
                    <textarea name="reason" rows="4" style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px;" placeholder="Reason for suspension..." required></textarea>
                </div>
            </div>
            <div style="padding: 20px; border-top: 1px solid #e5e7eb; display: flex; gap: 10px; justify-content: flex-end;">
                <button type="button" onclick="closeSuspendModal()" style="padding: 8px 20px; background: #f3f4f6; border: none; border-radius: 6px; cursor: pointer; font-size: 14px;">Cancel</button>
                <button type="submit" style="padding: 8px 20px; background: #dc2626; color: white; border: none; border-radius: 6px; cursor: pointer; font-size: 14px;">Suspend</button>
            </div>
        </form>
    </div>
</div>

<style>
    @keyframes modalFadeIn {
        from {
            opacity: 0;
            transform: translateY(-20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    .badge-orderer-badge {
        padding: 5px 10px;
        border-radius: 6px;
        font-size: 11px;
        font-weight: 600;
    }
</style>

<script>
    function openRejectModal(id, sellerName) {
        const modal = document.getElementById('rejectModal');
        const form = document.getElementById('rejectForm');
        const sellerInfo = document.getElementById('modalSellerInfo');
        
        form.action = "{{ route('admin.sellers.reject', ['seller' => '__ID__']) }}".replace('__ID__', id);
        sellerInfo.innerHTML = `<strong>${sellerName}</strong><br>This seller application will be rejected.`;
        
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }
    
    function closeRejectModal() {
        const modal = document.getElementById('rejectModal'); 
        modal.style.display = 'none';
        document.body.style.overflow = '';
    }
    
    function openSuspendModal(id, sellerName) {
        const modal = document.getElementById('suspendModal');
        const form = document.getElementById('suspendForm');
        const sellerInfo = document.getElementById('suspendSellerInfo');
        
        form.action = "{{ route('admin.sellers.suspend', ['seller' => '__ID__']) }}".replace('__ID__', id);
        sellerInfo.innerHTML = `<strong>${sellerName}</strong><br>This seller will be suspended and unable to list products.`;
        
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }
    
    function closeSuspendModal() {
        const modal = document.getElementById('suspendModal');
        modal.style.display = 'none';
        document.body.style.overflow = '';
    }
    
    // Close modals when clicking outside
    document.getElementById('rejectModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeRejectModal();
        }
    });
    
    document.getElementById('suspendModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeSuspendModal();
        }
    });
</script>

@endsection