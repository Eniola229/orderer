@extends('layouts.admin')
@section('title', 'Brands')
@section('page_title', 'Brand Management')
@section('breadcrumb')
    <li class="breadcrumb-item active">Brands</li>
@endsection

@section('content')

<form action="{{ route('admin.brands.index') }}" method="GET"
      class="d-flex gap-2 mb-4">
    <input type="text" name="search" class="form-control form-control-sm"
           placeholder="Search brand name..." value="{{ request('search') }}"
           style="width:280px;">
    <button type="submit" class="btn btn-sm btn-outline-primary">
        <i class="feather-search"></i>
    </button>
</form>

<div class="card">
    <div class="card-body p-0">
        @if($brands->count())
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Brand</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Seller</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Rating</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Reviews</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Status</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($brands as $brand)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                @if($brand->logo)
                                <img src="{{ $brand->logo }}"
                                     style="width:40px;height:40px;object-fit:contain;border-radius:6px;background:#f5f5f5;padding:2px;"
                                     alt="">
                                @else
                                <div style="width:40px;height:40px;border-radius:6px;background:#2ECC71;color:#fff;display:flex;align-items:center;justify-content:center;font-weight:700;">
                                    {{ strtoupper(substr($brand->name,0,1)) }}
                                </div>
                                @endif
                                <div>
                                    <p class="mb-0 fw-semibold fs-13">{{ $brand->name }}</p>
                                    @if($brand->website)
                                    <a href="{{ $brand->website }}" target="_blank"
                                       class="text-muted" style="font-size:11px;">
                                        {{ $brand->website }}
                                    </a>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td>
                            <a href="{{ route('admin.sellers.show', $brand->seller_id) }}"
                               class="fs-13 text-primary">
                                {{ $brand->seller->business_name ?? '—' }}
                            </a>
                        </td>
                        <td>
                            <span class="text-warning">★</span>
                            <strong>{{ number_format($brand->average_rating, 1) }}</strong>
                        </td>
                        <td class="fw-semibold">{{ $brand->total_reviews }}</td>
                        <td>
                            <span class="badge orderer-badge {{ $brand->is_active ? 'badge-approved' : 'badge-rejected' }}">
                                {{ $brand->is_active ? 'Active' : 'Suspended' }}
                            </span>
                        </td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('brands.show', $brand->slug) }}" target="_blank"
                                   class="btn btn-xs btn-outline-secondary"
                                   style="font-size:11px;padding:2px 8px;">
                                    View
                                </a>
                                @if(auth('admin')->user()->canModerateSellers())
                                @if($brand->is_active)
                                <form action="{{ route('admin.brands.suspend', $brand->id) }}" method="POST">
                                    @csrf @method('PUT')
                                    <button type="submit"
                                            class="btn btn-xs btn-outline-danger"
                                            style="font-size:11px;padding:2px 8px;"
                                            onclick="return confirm('Suspend this brand?')">
                                        Suspend
                                    </button>
                                </form>
                                @else
                                <form action="{{ route('admin.brands.activate', $brand->id) }}" method="POST">
                                    @csrf @method('PUT')
                                    <button type="submit"
                                            class="btn btn-xs btn-outline-success"
                                            style="font-size:11px;padding:2px 8px;">
                                        Activate
                                    </button>
                                </form>
                                @endif
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="p-3">{{ $brands->links() }}</div>
        @else
        <div class="text-center py-5 text-muted">
            <i class="feather-award mb-2 d-block" style="font-size:40px;"></i>
            <p>No brands found.</p>
        </div>
        @endif
    </div>
</div>

@endsection