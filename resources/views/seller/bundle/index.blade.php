@extends('layouts.seller')
@section('title', 'Product Bundles')
@section('page_title', 'Product Bundles')
@section('breadcrumb')
    <li class="breadcrumb-item active">Bundles</li>
@endsection
@section('page_actions')
    <a href="{{ route('seller.bundles.create') }}" class="btn btn-primary btn-sm">
        <i class="feather-plus me-1"></i> Create Bundle
    </a>
@endsection

@section('content')
<div class="card">
    <div class="card-body p-0">
        @if($bundles->count())
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Bundle</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Items</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Original</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Bundle Price</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Savings</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Status</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($bundles as $bundle)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                @if($bundle->bundle_image)
                                <img src="{{ $bundle->bundle_image }}"
                                     style="width:44px;height:44px;object-fit:cover;border-radius:8px;" alt="">
                                @else
                                <div style="width:44px;height:44px;background:#f5f5f5;border-radius:8px;display:flex;align-items:center;justify-content:center;">
                                    <i class="feather-package text-muted"></i>
                                </div>
                                @endif
                                <p class="mb-0 fw-semibold fs-13">
                                    {{ Str::limit($bundle->name, 35) }}
                                </p>
                            </div>
                        </td>
                        <td class="fw-semibold">{{ $bundle->items->count() }} items</td>
                        <td class="text-muted text-decoration-line-through">
                            ₦{{ number_format($bundle->original_total, 2) }}
                        </td>
                        <td class="fw-bold text-success">
                            ₦{{ number_format($bundle->bundle_price, 2) }}
                        </td>
                        <td>
                            <span style="background:#D5F5E3;color:#1E8449;padding:2px 10px;border-radius:10px;font-size:12px;font-weight:700;">
                                Save ${{ number_format($bundle->savings, 2) }}
                            </span>
                        </td>
                        <td>
                            <span class="badge orderer-badge badge-{{ $bundle->status }}">
                                {{ ucfirst($bundle->status) }}
                            </span>
                        </td>
                        <td>
                            <form action="{{ route('seller.bundles.destroy', $bundle->id) }}"
                                  method="POST"
                                  onsubmit="return confirm('Delete this bundle?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                    <i class="feather-trash-2"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="p-3">{{ $bundles->links() }}</div>
        @else
        <div class="text-center py-5 text-muted">
            <i class="feather-package mb-2 d-block" style="font-size:40px;"></i>
            <p>No bundles yet. Create a bundle to offer buyers a discounted set of products.</p>
            <a href="{{ route('seller.bundles.create') }}" class="btn btn-primary">
                Create Bundle
            </a>
        </div>
        @endif
    </div>
</div>
@endsection