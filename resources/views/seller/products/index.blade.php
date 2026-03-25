@extends('layouts.seller')
@section('title', 'Products')
@section('page_title', 'My Products')
@section('breadcrumb')
    <li class="breadcrumb-item active">Products</li>
@endsection
@section('page_actions')
    <a href="{{ route('seller.products.create') }}" class="btn btn-primary btn-sm">
        <i class="feather-plus me-1"></i> Add Product
    </a>
@endsection

@section('content')

{{-- Status filter tabs --}}
<div class="d-flex gap-2 mb-3 flex-wrap">
    @foreach(['all','pending','approved','rejected','draft','suspended'] as $tab)
    <a href="{{ route('seller.products.index', ['status' => $tab]) }}"
       class="btn btn-sm {{ request('status','all') === $tab ? 'btn-primary' : 'btn-outline-secondary' }}">
        {{ ucfirst($tab) }}
    </a>
    @endforeach
</div>

<div class="card">
    <div class="card-body p-0">
        @if($products->count())
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Product</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Category</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Price</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Stock</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Status</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Date</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($products as $product)
                    @php $img = $product->images->where('is_primary', true)->first() ?? $product->images->first(); @endphp
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-3">
                                @if($img)
                                    <img src="{{ $img->image_url }}"
                                         style="width:46px;height:46px;object-fit:cover;border-radius:8px;border:1px solid #eee;"
                                         alt="">
                                @else
                                    <div style="width:46px;height:46px;background:#f5f5f5;border-radius:8px;display:flex;align-items:center;justify-content:center;">
                                        <i class="feather-image text-muted"></i>
                                    </div>
                                @endif
                                <div>
                                    <p class="mb-0 fw-semibold fs-13">{{ Str::limit($product->name, 40) }}</p>
                                    @if($product->sku)
                                        <small class="text-muted">SKU: {{ $product->sku }}</small>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="fs-13 text-muted">{{ $product->category->name ?? '—' }}</td>
                        <td>
                            @if($product->sale_price)
                                <small class="text-muted text-decoration-line-through">
                                    ${{ number_format($product->price, 2) }}
                                </small><br>
                                <span class="fw-bold text-success">
                                    ${{ number_format($product->sale_price, 2) }}
                                </span>
                            @else
                                <span class="fw-bold">${{ number_format($product->price, 2) }}</span>
                            @endif
                        </td>
                        <td>
                            <span class="fw-semibold {{ $product->stock <= 5 ? 'text-danger' : '' }}">
                                {{ $product->stock }}
                            </span>
                        </td>
                        <td>
                            <span class="badge orderer-badge badge-{{ $product->status }}">
                                {{ ucfirst($product->status) }}
                            </span>
                            @if($product->status === 'rejected' && $product->rejection_reason)
                                <p class="fs-11 text-danger mb-0 mt-1">
                                    {{ Str::limit($product->rejection_reason, 40) }}
                                </p>
                            @endif
                        </td>
                        <td class="text-muted fs-12">{{ $product->created_at->format('M d, Y') }}</td>
                        <td>
                            <div class="d-flex gap-2">
                                <a href="{{ route('seller.products.edit', $product->id) }}"
                                   class="btn btn-sm btn-outline-primary">
                                    <i class="feather-edit-2"></i>
                                </a>
                                <form action="{{ route('seller.products.destroy', $product->id) }}"
                                      method="POST"
                                      onsubmit="return confirm('Delete this product permanently?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        <i class="feather-trash-2"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="p-3">{{ $products->links() }}</div>
        @else
        <div class="text-center py-5 text-muted">
            <i class="feather-package mb-3 d-block" style="font-size:40px;"></i>
            <p class="mb-3">No products found.</p>
        </div>
        @endif
    </div>
</div>

@endsection