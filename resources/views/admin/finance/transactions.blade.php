@extends('layouts.admin')
@section('title', 'Transactions')
@section('page_title', 'Transaction History')
@section('breadcrumb')
    <li class="breadcrumb-item active">Finance</li>
    <li class="breadcrumb-item active">Transactions</li>
@endsection

@section('content')

<style>
    .transaction-row:hover {
        background-color: #f8f9fa;
        cursor: pointer;
    }
    .transaction-details td {
        border-top: none !important;
    }
    .feather-chevron-right, .feather-chevron-down {
        transition: transform 0.3s ease;
    }
    .filter-card {
        background: #f8f9fa;
        border-radius: 8px;
        margin-bottom: 20px;
    }
</style>

<div class="row mb-4">
    <div class="col-md-4 mb-2">
        <div class="card">
            <div class="card-body">
                <p class="text-muted fs-12 fw-semibold text-uppercase mb-1">Total Volume</p>
                <h2 class="fw-bold mb-0 text-success">₦{{ number_format($stats['total_volume'], 2) }}</h2>
                <small class="text-muted">All successful payments</small>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-2">
        <div class="card">
            <div class="card-body">
                <p class="text-muted fs-12 fw-semibold text-uppercase mb-1">In Escrow</p>
                <h2 class="fw-bold mb-0 text-warning">${{ number_format($stats['in_escrow'], 2) }}</h2>
                <small class="text-muted">Held pending delivery</small>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-2">
        <div class="card">
            <div class="card-body">
                <p class="text-muted fs-12 fw-semibold text-uppercase mb-1">Total Released</p>
                <h2 class="fw-bold mb-0 text-primary">${{ number_format($stats['total_released'], 2) }}</h2>
                <small class="text-muted">Paid out to sellers</small>
            </div>
        </div>
    </div>
</div>

{{-- Filters --}}
<div class="card filter-card">
    <div class="card-body">
        <form action="{{ route('admin.finance.transactions') }}" method="GET" class="row g-3">
            <div class="col-md-3">
                <label class="form-label fw-semibold fs-12">Transaction Type</label>
                <select name="type" class="form-select form-select-sm">
                    <option value="">All Types</option>
                    <option value="credit" {{ request('type') == 'credit' ? 'selected' : '' }}>Credit</option>
                    <option value="debit" {{ request('type') == 'debit' ? 'selected' : '' }}>Debit</option>
                    <option value="escrow_hold" {{ request('type') == 'escrow_hold' ? 'selected' : '' }}>Escrow Hold</option>
                    <option value="escrow_release" {{ request('type') == 'escrow_release' ? 'selected' : '' }}>Escrow Release</option>
                    <option value="escrow_refund" {{ request('type') == 'escrow_refund' ? 'selected' : '' }}>Escrow Refund</option>
                    <option value="ads_debit" {{ request('type') == 'ads_debit' ? 'selected' : '' }}>Ads Debit</option>
                    <option value="commission_debit" {{ request('type') == 'commission_debit' ? 'selected' : '' }}>Commission Debit</option>
                    <option value="withdrawal" {{ request('type') == 'withdrawal' ? 'selected' : '' }}>Withdrawal</option>
                    <option value="referral_credit" {{ request('type') == 'referral_credit' ? 'selected' : '' }}>Referral Credit</option>
                    <option value="refund" {{ request('type') == 'refund' ? 'selected' : '' }}>Refund</option>
                </select>
            </div>
            
            <div class="col-md-2">
                <label class="form-label fw-semibold fs-12">Status</label>
                <select name="status" class="form-select form-select-sm">
                    <option value="">All Status</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Failed</option>
                    <option value="reversed" {{ request('status') == 'reversed' ? 'selected' : '' }}>Reversed</option>
                </select>
            </div>
            
            <div class="col-md-2">
                <label class="form-label fw-semibold fs-12">Owner Type</label>
                <select name="owner_type" class="form-select form-select-sm">
                    <option value="">All Owners</option>
                    <option value="seller" {{ request('owner_type') == 'seller' ? 'selected' : '' }}>Sellers</option>
                    <option value="buyer" {{ request('owner_type') == 'buyer' ? 'selected' : '' }}>Buyers</option>
                </select>
            </div>
            
            <div class="col-md-3">
                <label class="form-label fw-semibold fs-12">Search</label>
                <input type="text" name="search" class="form-control form-control-sm" 
                       placeholder="Reference, description, email..." 
                       value="{{ request('search') }}">
            </div>
            
            <div class="col-md-2">
                <label class="form-label fw-semibold fs-12">Date From</label>
                <input type="date" name="date_from" class="form-control form-control-sm" 
                       value="{{ request('date_from') }}">
            </div>
            
            <div class="col-md-2">
                <label class="form-label fw-semibold fs-12">Date To</label>
                <input type="date" name="date_to" class="form-control form-control-sm" 
                       value="{{ request('date_to') }}">
            </div>
            
            <div class="col-md-12 d-flex justify-content-end gap-2">
                <a href="{{ route('admin.finance.transactions') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="feather-x me-1"></i> Clear
                </a>
                <button type="submit" class="btn btn-sm btn-primary">
                    <i class="feather-filter me-1"></i> Apply Filters
                </button>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">Wallet Transactions</h5>
        @if(request()->anyFilled(['type', 'status', 'owner_type', 'search', 'date_from', 'date_to']))
            <small class="text-muted">
                <i class="feather-filter me-1"></i> Filtered results
            </small>
        @endif
    </div>
    <div class="card-body p-0">
        @if($transactions->count())
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    践
                        <th class="fs-11 text-uppercase text-muted fw-semibold" width="5%"></th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Reference</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Owner</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Type</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Amount</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Balance After</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Status</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Date</th>
                     </thead>
                <tbody>
                    @foreach($transactions as $txn)
                    @php
                        $isCredit = in_array($txn->type, [
                            'credit','escrow_release','referral_credit',
                            'escrow_refund','refund','ads_credit',
                        ]);
                        
                        // Get owner from the wallet's walletable relationship
                        $owner = $txn->wallet ? $txn->wallet->walletable : null;
                        
                        // Determine owner name based on type
                        if ($owner) {
                            if (get_class($owner) === 'App\Models\Seller') {
                                $ownerName = $owner->business_name ?? ($owner->first_name . ' ' . $owner->last_name);
                                $ownerType = 'Seller';
                            } elseif (get_class($owner) === 'App\Models\User') {
                                $ownerName = $owner->first_name . ' ' . $owner->last_name;
                                $ownerType = 'Buyer';
                            } else {
                                $ownerName = class_basename($owner);
                                $ownerType = 'Unknown';
                            }
                        } else {
                            $ownerName = '—';
                            $ownerType = '—';
                        }
                        
                        $statusColors = [
                            'completed' => '#28a745',
                            'pending' => '#ffc107',
                            'failed' => '#dc3545',
                            'reversed' => '#6c757d',
                        ];
                        $statusColor = $statusColors[$txn->status] ?? '#6c757d';
                    @endphp
                    <tr class="transaction-row" data-id="{{ $txn->id }}" style="cursor: pointer;">
                        <td class="text-center">
                            <i class="feather-chevron-right" id="icon-{{ $txn->id }}" style="font-size: 16px; color: #6c757d;"></i>
                          
                          
                        <td><code class="fs-11">{{ Str::limit($txn->reference, 20) }}</code>  
                        <td class="fs-13">
                            <strong>{{ $ownerName }}</strong>
                            <small class="text-muted d-block">{{ $ownerType }}</small>
                          
                        <td>
                            <span class="badge orderer-badge {{ $isCredit ? 'badge-approved' : 'badge-pending' }}">
                                {{ str_replace('_', ' ', ucfirst($txn->type)) }}
                            </span>
                          
                        <td class="fw-bold {{ $isCredit ? 'text-success' : 'text-danger' }}">
                            {{ $isCredit ? '+' : '-' }}₦{{ number_format($txn->amount, 2) }}
                          
                        <td class="fw-semibold">${{ number_format($txn->balance_after, 2) }}  
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
                          
                        <td class="text-muted fs-12">{{ $txn->created_at->format('M d, Y H:i') }}  
                      </tr>
                    <tr class="transaction-details" id="details-{{ $txn->id }}" style="display: none;">
                        <td colspan="8" class="bg-light">
                            <div style="padding: 20px;">
                                <h6 class="mb-3 text-primary">
                                    <i class="feather-info me-2"></i>Transaction Details
                                </h6>
                                <div class="row g-3">
                                    <div class="col-md-3">
                                        <div class="p-3 border rounded bg-white">
                                            <label class="fs-11 text-muted mb-1 d-block">Reference</label>
                                            <code class="fs-12">{{ $txn->reference }}</code>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="p-3 border rounded bg-white">
                                            <label class="fs-11 text-muted mb-1 d-block">Transaction ID</label>
                                            <code class="fs-12">{{ $txn->id }}</code>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="p-3 border rounded bg-white">
                                            <label class="fs-11 text-muted mb-1 d-block">Type</label>
                                            <strong>{{ str_replace('_', ' ', ucfirst($txn->type)) }}</strong>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="p-3 border rounded bg-white">
                                            <label class="fs-11 text-muted mb-1 d-block">Status</label>
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
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="p-3 border rounded bg-white">
                                            <label class="fs-11 text-muted mb-1 d-block">Amount</label>
                                            <strong class="{{ $isCredit ? 'text-success' : 'text-danger' }}">
                                                {{ $isCredit ? '+' : '-' }}₦{{ number_format($txn->amount, 2) }}
                                            </strong>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="p-3 border rounded bg-white">
                                            <label class="fs-11 text-muted mb-1 d-block">Balance Before</label>
                                            <strong>₦{{ number_format($txn->balance_before, 2) }}</strong>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="p-3 border rounded bg-white">
                                            <label class="fs-11 text-muted mb-1 d-block">Balance After</label>
                                            <strong>₦{{ number_format($txn->balance_after, 2) }}</strong>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="p-3 border rounded bg-white">
                                            <label class="fs-11 text-muted mb-1 d-block">Owner</label>
                                            <strong>{{ $ownerName }}</strong>
                                            <small class="text-muted d-block">{{ $ownerType }}</small>
                                            @if($owner && ($owner->email ?? false))
                                                <small class="text-muted">{{ $owner->email }}</small>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="p-3 border rounded bg-white">
                                            <label class="fs-11 text-muted mb-1 d-block">Wallet ID</label>
                                            <code class="fs-12">{{ $txn->wallet_id }}</code>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="p-3 border rounded bg-white">
                                            <label class="fs-11 text-muted mb-1 d-block">Description</label>
                                            <p class="mb-0">{{ $txn->description ?? 'No description' }}</p>
                                        </div>
                                    </div>
                                    @if($txn->related_type && $txn->related_id)
                                    <div class="col-md-6">
                                        <div class="p-3 border rounded bg-white">
                                            <label class="fs-11 text-muted mb-1 d-block">Related To</label>
                                            <strong>{{ str_replace('_', ' ', ucfirst($txn->related_type)) }}</strong>
                                            <small class="text-muted d-block">ID: {{ $txn->related_id }}</small>
                                        </div>
                                    </div>
                                    @endif
                                    <div class="col-md-6">
                                        <div class="p-3 border rounded bg-white">
                                            <label class="fs-11 text-muted mb-1 d-block">Created At</label>
                                            <strong>{{ $txn->created_at->format('M d, Y H:i:s') }}</strong>
                                        </div>
                                    </div>
                                    @if($txn->updated_at != $txn->created_at)
                                    <div class="col-md-6">
                                        <div class="p-3 border rounded bg-white">
                                            <label class="fs-11 text-muted mb-1 d-block">Last Updated</label>
                                            <strong>{{ $txn->updated_at->format('M d, Y H:i:s') }}</strong>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>
                          
                      </tr>
                    @endforeach
                </tbody>
              </table>
        </div>
        <div class="p-3">{{ $transactions->links() }}</div>
        @else
        <div class="text-center py-5 text-muted">
            <i class="feather-activity mb-2 d-block" style="font-size:40px;"></i>
            <p>No transactions found.</p>
        </div>
        @endif
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const rows = document.querySelectorAll('.transaction-row');
        
        rows.forEach(row => {
            row.addEventListener('click', function(e) {
                if (e.target.tagName === 'A' || e.target.tagName === 'BUTTON') {
                    return;
                }
                
                const transactionId = this.getAttribute('data-id');
                const detailsRow = document.getElementById(`details-${transactionId}`);
                const chevron = this.querySelector(`#icon-${transactionId}`);
                
                if (detailsRow.style.display === 'none' || !detailsRow.style.display) {
                    document.querySelectorAll('.transaction-details').forEach(detail => {
                        detail.style.display = 'none';
                    });
                    document.querySelectorAll('.feather-chevron-right, .feather-chevron-down').forEach(icon => {
                        icon.className = 'feather-chevron-right';
                        icon.style.transform = 'rotate(0deg)';
                    });
                    
                    detailsRow.style.display = 'table-row';
                    if (chevron) {
                        chevron.className = 'feather-chevron-down';
                        chevron.style.transform = 'rotate(0deg)';
                    }
                } else {
                    detailsRow.style.display = 'none';
                    if (chevron) {
                        chevron.className = 'feather-chevron-right';
                    }
                }
            });
        });
    });
</script>

@endsection