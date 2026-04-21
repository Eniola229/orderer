@extends('layouts.admin')
@section('title', 'Pending Products')
@section('page_title', 'Products Awaiting Review')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.products.index') }}">Products</a></li>
    <li class="breadcrumb-item active">Pending</li>
@endsection

@section('content')

@if($products->count())
<div class="row">
    @foreach($products as $product)
    @php $img = $product->images->where('is_primary',true)->first() ?? $product->images->first(); @endphp
    <div class="col-lg-6 mb-4">
        <div class="card h-100">
            <div class="card-header d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="mb-0 fw-bold">{{ Str::limit($product->name, 45) }}</h6>
                    <small class="text-muted">
                        by
                        <a href="{{ route('admin.sellers.show', $product->seller_id) }}" class="text-primary">
                            {{ $product->seller->business_name ?? '—' }}
                        </a>
                        · {{ $product->created_at->format('M d, Y H:i') }}
                    </small>
                </div>
                <span class="badge orderer-badge badge-pending">Pending</span>
            </div>

            @if($img)
            <div style="height:200px;overflow:hidden;background:#f8f8f8;">
                <img src="{{ $img->image_url }}"
                     style="width:100%;height:100%;object-fit:cover;" alt="">
            </div>
            @endif

            <div class="card-body">
                <div class="row g-3 mb-3">
                    <div class="col-6">
                        <small class="text-muted d-block">Category</small>
                        <strong class="fs-13">{{ $product->category->name ?? '—' }}</strong>
                    </div>
                    <div class="col-6">
                        <small class="text-muted d-block">Condition</small>
                        <strong class="fs-13">{{ ucfirst($product->condition) }}</strong>
                    </div>
                    <div class="col-6">
                        <small class="text-muted d-block">Price</small>
                        <strong class="fs-13 text-success">₦{{ number_format($product->price, 2) }}</strong>
                        @if($product->sale_price)
                            <small class="text-muted ms-1">(Sale: ₦{{ number_format($product->sale_price, 2) }})</small>
                        @endif
                    </div>
                    <div class="col-6">
                        <small class="text-muted d-block">Stock</small>
                        <strong class="fs-13">{{ $product->stock }} units</strong>
                    </div>
                    @if($product->weight_kg)
                    <div class="col-6">
                        <small class="text-muted d-block">Weight</small>
                        <strong class="fs-13">{{ $product->weight_kg }} kg</strong>
                    </div>
                    @endif
                    <div class="col-6">
                        <small class="text-muted d-block">Images</small>
                        <strong class="fs-13">{{ $product->images->count() }} uploaded</strong>
                    </div>
                </div>

                @if($product->description)
                <div class="mb-3">
                    <small class="text-muted d-block mb-1">Description</small>
                    <p class="fs-13 text-muted mb-0">{{ Str::limit($product->description, 200) }}</p>
                </div>
                @endif

                {{-- Image strip --}}
                @if($product->images->count() > 1)
                <div class="d-flex gap-2 mb-3 flex-wrap">
                    @foreach($product->images->take(5) as $pimg)
                    <img src="{{ $pimg->image_url }}"
                         style="width:56px;height:56px;object-fit:cover;border-radius:6px;border:1px solid #eee;"
                         alt="">
                    @endforeach
                </div>
                @endif

                @if(auth('admin')->user()->canModerateSellers())
                <div class="d-flex gap-2">
                    <form action="{{ route('admin.products.approve', $product->id) }}" method="POST">
                        @csrf @method('PUT')
                        <button type="submit" class="btn btn-success btn-sm">
                            <i class="feather-check me-1"></i> Approve
                        </button>
                    </form>
                    <button type="button"
                            class="btn btn-danger btn-sm"
                            data-bs-toggle="modal"
                            data-bs-target="#rejectProd{{ $product->id }}">
                        <i class="feather-x me-1"></i> Reject
                    </button>
                    <a href="{{ route('admin.products.show', $product) }}" target="_blank"
                       class="btn btn-outline-secondary btn-sm">
                        <i class="feather-eye me-1"></i> Preview
                    </a>
                </div>
                @else
                <div class="alert alert-warning mb-0">
                    <i class="feather-lock me-2"></i>
                    You don't have permission to approve products.
                </div>
                @endif
            </div>
        </div>

        {{-- Reject Modal --}}
        <div class="modal fade" id="rejectProd{{ $product->id }}" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Reject Product</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="{{ route('admin.products.reject', $product->id) }}" method="POST">
                        @csrf @method('PUT')
                        <div class="modal-body">
                            <label class="form-label fw-bold">Reason for rejection</label>
                            <textarea name="reason" class="form-control" rows="3"
                                      placeholder="Be specific — the seller will see this..."
                                      required></textarea>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-danger">Reject</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>
<div class="d-flex justify-content-center">{{ $products->links() }}</div>
@else
<div class="card">
    <div class="card-body text-center py-5 text-muted">
        <i class="feather-check-circle mb-2 d-block" style="font-size:40px;color:#2ECC71;"></i>
        <p>No products awaiting review. All caught up!</p>
    </div>
</div>
@endif

@endsection