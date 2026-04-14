@extends('layouts.admin')
@section('title', 'Newsletter')
@section('page_title', 'Newsletter')
@section('breadcrumb')
    <li class="breadcrumb-item active">Newsletter</li>
@endsection
@section('content')

{{-- Action bar --}}
<div class="row mb-3">
    <div class="col d-flex gap-2">
        <a href="{{ route('admin.newsletter.create') }}" class="btn btn-primary">
            <i class="feather-plus me-1"></i> Compose Newsletter
        </a>
        <button class="btn btn-outline-secondary" id="btnViewSubscribers">
            <i class="feather-users me-1"></i> View Subscribers
        </button>
    </div>
</div>

{{-- Newsletters table --}}
<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">All Newsletters</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Subject</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Audience</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Status</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Recipients</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Sent By</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Date</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($newsletters as $nl)
                    <tr>
                        <td class="fw-semibold">{{ $nl->subject }}</td>
                        <td>
                            <span class="badge bg-secondary">{{ $nl->audience_label }}</span>
                        </td>
                        <td>
                            @php
                                $badgeMap = [
                                    'draft'   => 'secondary',
                                    'queued'  => 'warning',
                                    'sending' => 'info',
                                    'sent'    => 'success',
                                    'failed'  => 'danger',
                                ];
                                $color = $badgeMap[$nl->status] ?? 'secondary';
                            @endphp
                            <span class="badge bg-{{ $color }}">{{ ucfirst($nl->status) }}</span>
                        </td>
                        <td>
                            @if($nl->total_recipients)
                                {{ number_format($nl->sent_count) }} / {{ number_format($nl->total_recipients) }}
                                <div class="progress mt-1" style="height:4px;width:80px;">
                                    <div class="progress-bar bg-success"
                                         style="width:{{ $nl->progress_percent }}%"></div>
                                </div>
                            @else
                                —
                            @endif
                        </td>
                        <td class="fs-12 text-muted">{{ $nl->creator->full_name ?? '—' }}</td>
                        <td class="fs-12 text-muted">{{ $nl->created_at->format('M d, Y') }}</td>
                        <td>
                            <a href="{{ route('admin.newsletter.show', $nl) }}"
                               class="btn btn-sm btn-outline-primary">View</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">No newsletters yet.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($newsletters->hasPages())
    <div class="card-footer">
        {{ $newsletters->links() }}
    </div>
    @endif
</div>

{{-- Subscribers Modal --}}
<div class="modal fade" id="subscribersModal" tabindex="-1" aria-labelledby="subscribersModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="subscribersModalLabel">
                    Newsletter Subscribers
                    <span class="badge bg-primary ms-2 fs-12" id="subscriberCount">—</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">

                {{-- Date filters --}}
                <div class="row g-2 mb-3">
                    <div class="col-sm-4">
                        <label class="form-label fs-12 text-muted mb-1">From</label>
                        <input type="date" id="filterFrom" class="form-control form-control-sm">
                    </div>
                    <div class="col-sm-4">
                        <label class="form-label fs-12 text-muted mb-1">To</label>
                        <input type="date" id="filterTo" class="form-control form-control-sm">
                    </div>
                    <div class="col-sm-4 d-flex align-items-end gap-2">
                        <button class="btn btn-sm btn-primary w-50" id="btnApplyFilter">Filter</button>
                        <button class="btn btn-sm btn-outline-secondary w-50" id="btnClearFilter">Clear</button>
                    </div>
                </div>

                {{-- Per Page Selector --}}
                <div class="row mb-3">
                    <div class="col-sm-4">
                        <label class="form-label fs-12 text-muted mb-1">Show per page</label>
                        <select id="perPageSelect" class="form-select form-select-sm">
                            <option value="50">50</option>
                            <option value="100" selected>100</option>
                            <option value="200">200</option>
                            <option value="500">500</option>
                        </select>
                    </div>
                </div>

                {{-- Table --}}
                <div class="table-responsive">
                    <table class="table table-sm table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="fs-11 text-uppercase text-muted fw-semibold">#</th>
                                <th class="fs-11 text-uppercase text-muted fw-semibold">Email</th>
                                <th class="fs-11 text-uppercase text-muted fw-semibold">Subscribed</th>
                            </tr>
                        </thead>
                        <tbody id="subscriberTableBody">
                            <tr>
                                <td colspan="3" class="text-center text-muted py-4">
                                    <div class="spinner-border spinner-border-sm me-2"></div> Loading…
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                {{-- Pagination Controls --}}
                <div class="d-flex justify-content-between align-items-center mt-3" id="paginationControls" style="display: none;">
                    <div class="text-muted fs-12" id="paginationInfo"></div>
                    <div>
                        <button class="btn btn-sm btn-outline-primary" id="prevPageBtn" disabled>
                            <i class="feather-chevron-left"></i> Previous
                        </button>
                        <button class="btn btn-sm btn-outline-primary ms-2" id="nextPageBtn">
                            Next <i class="feather-chevron-right"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    /* COMPLETELY DISABLE ALL BLUR EFFECTS */
    .modal.show .modal-backdrop,
    .modal-backdrop.show,
    .modal-backdrop {
        backdrop-filter: none !important;
        -webkit-backdrop-filter: none !important;
        filter: none !important;
        -webkit-filter: none !important;
        background-color: rgba(0, 0, 0, 0.5) !important;
        transition: none !important;
        animation: none !important;
    }
    
    /* Remove any blur from body when modal is open */
    body.modal-open,
    body:has(.modal.show) {
        filter: none !important;
        -webkit-filter: none !important;
        backdrop-filter: none !important;
        overflow: hidden !important;
        padding-right: 0 !important;
    }
    
    /* Target all possible containers */
    body.modal-open *,
    body:has(.modal.show) * {
        filter: none !important;
        -webkit-filter: none !important;
        backdrop-filter: none !important;
    }
    
    /* Specifically target the wrapper/main content */
    .wrapper,
    #main-wrapper,
    .main-content,
    .content-wrapper,
    .page-wrapper,
    .app-main,
    .app-content {
        transition: none !important;
        filter: none !important;
        backdrop-filter: none !important;
    }
    
    /* Disable any theme-specific modal transitions */
    .modal.fade .modal-dialog {
        transition: transform 0.3s ease-out !important;
    }
    
    .modal-backdrop.fade {
        transition: opacity 0.15s linear !important;
    }
    
    /* Force modal backdrop to not have blur */
    .modal-backdrop {
        opacity: 0.5 !important;
    }
</style>
@endpush

@push('scripts')
<script>
(function () {
    const endpoint = "{{ route('admin.newsletter.subscribers') }}";
    let currentPage = 1;
    let lastPage = 1;
    let totalSubscribers = 0;
    
    function fetchSubscribers(page = 1) {
        const from = document.getElementById('filterFrom').value;
        const to = document.getElementById('filterTo').value;
        const perPage = document.getElementById('perPageSelect').value;

        const params = new URLSearchParams();
        if (from) params.append('date_from', from);
        if (to) params.append('date_to', to);
        params.append('page', page);
        params.append('per_page', perPage);
        
        const fullUrl = `${endpoint}?${params.toString()}`;
        console.log('Fetching from URL:', fullUrl);

        const tbody = document.getElementById('subscriberTableBody');
        tbody.innerHTML = `<tr><td colspan="3" class="text-center text-muted py-3">
            <div class="spinner-border spinner-border-sm me-2"></div> Loading...
        </td></tr>`;

        fetch(fullUrl, {
            method: 'GET',
            headers: { 
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            credentials: 'same-origin'
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('Response data:', data);
            
            if (!data.success) {
                throw new Error(data.error || 'Unknown error');
            }
            
            document.getElementById('subscriberCount').textContent = data.total.toLocaleString();
            totalSubscribers = data.total;
            currentPage = data.current_page;
            lastPage = data.last_page;
            
            // Update pagination UI
            updatePaginationUI();
            
            if (!data.subscribers || !data.subscribers.length) {
                tbody.innerHTML = `<tr><td colspan="3" class="text-center text-muted py-4">No subscribers found.</td></tr>`;
                return;
            }

            const startNumber = ((currentPage - 1) * data.per_page) + 1;
            tbody.innerHTML = data.subscribers.map((s, i) => `
                <tr>
                    <td class="text-muted fs-12">${startNumber + i}</td>
                    <td>${escapeHtml(s.email)}</td>
                    <td class="text-muted fs-12">${s.subscribed_at}</td>
                </tr>
            `).join('');
        })
        .catch(error => {
            console.error('Fetch error details:', error);
            tbody.innerHTML = `<tr><td colspan="3" class="text-center text-danger py-4">
                Failed to load subscribers.<br>
                URL: ${fullUrl}<br>
                Error: ${error.message}
            </td></tr>`;
        });
    }
    
    function updatePaginationUI() {
        const paginationDiv = document.getElementById('paginationControls');
        const prevBtn = document.getElementById('prevPageBtn');
        const nextBtn = document.getElementById('nextPageBtn');
        const paginationInfo = document.getElementById('paginationInfo');
        
        if (lastPage > 1) {
            paginationDiv.style.display = 'flex';
            const start = ((currentPage - 1) * parseInt(document.getElementById('perPageSelect').value)) + 1;
            const end = Math.min(start + parseInt(document.getElementById('perPageSelect').value) - 1, totalSubscribers);
            paginationInfo.textContent = `Showing ${start} to ${end} of ${totalSubscribers.toLocaleString()} subscribers`;
            
            prevBtn.disabled = (currentPage <= 1);
            nextBtn.disabled = (currentPage >= lastPage);
        } else {
            paginationDiv.style.display = 'none';
        }
    }
    
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // Event Listeners
    document.getElementById('btnViewSubscribers').addEventListener('click', function () {
        console.log('View Subscribers button clicked');
        currentPage = 1;
        const modalElement = document.getElementById('subscribersModal');
        const modal = new bootstrap.Modal(modalElement);
        modal.show();
        fetchSubscribers(1);
    });

    document.getElementById('btnApplyFilter').addEventListener('click', function() {
        console.log('Apply filter clicked');
        fetchSubscribers(1);
    });

    document.getElementById('btnClearFilter').addEventListener('click', function () {
        console.log('Clear filter clicked');
        document.getElementById('filterFrom').value = '';
        document.getElementById('filterTo').value = '';
        fetchSubscribers(1);
    });
    
    document.getElementById('perPageSelect').addEventListener('change', function() {
        console.log('Per page changed');
        fetchSubscribers(1);
    });
    
    document.getElementById('prevPageBtn').addEventListener('click', function() {
        if (currentPage > 1) {
            console.log('Previous page clicked');
            fetchSubscribers(currentPage - 1);
        }
    });
    
    document.getElementById('nextPageBtn').addEventListener('click', function() {
        if (currentPage < lastPage) {
            console.log('Next page clicked');
            fetchSubscribers(currentPage + 1);
        }
    });
})();
</script>
@endpush
@endsection