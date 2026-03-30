@extends('layouts.seller')
@section('title', 'Properties')
@section('page_title', 'My Properties')
@section('breadcrumb')
    <li class="breadcrumb-item active">Properties</li>
@endsection
@section('page_actions')
    <a href="{{ route('seller.houses.create') }}" class="btn btn-primary btn-sm">
        <i class="feather-plus me-1"></i> Add Property
    </a>
@endsection

@section('content')

{{-- Status filter tabs --}}
<div class="d-flex gap-2 mb-3 flex-wrap">
    @foreach(['all','pending','approved','rejected','draft','suspended'] as $tab)
    <a href="{{ route('seller.houses.index', ['status' => $tab]) }}"
       class="btn btn-sm {{ request('status','all') === $tab ? 'btn-primary' : 'btn-outline-secondary' }}">
        {{ ucfirst($tab) }}
    </a>
    @endforeach
</div>

<div class="card">
    <div class="card-body p-0">
        @if($houses->count())
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Property</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Type</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Listing</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Price</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Location</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Status</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Date</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($houses as $house)
                    @php $img = $house->images->where('is_primary', true)->first() ?? $house->images->first(); @endphp
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-3">
                                @if($img)
                                    <img src="{{ $img->image_url }}"
                                         style="width:46px;height:46px;object-fit:cover;border-radius:8px;border:1px solid #eee;"
                                         alt="">
                                @else
                                    <div style="width:46px;height:46px;background:#f5f5f5;border-radius:8px;display:flex;align-items:center;justify-content:center;">
                                        <i class="feather-home text-muted"></i>
                                    </div>
                                @endif
                                <div>
                                    <p class="mb-0 fw-semibold fs-13">{{ Str::limit($house->title, 35) }}</p>
                                    @if($house->bedrooms || $house->bathrooms)
                                        <small class="text-muted">
                                            @if($house->bedrooms)
                                                <i class="feather-home me-1"></i>{{ $house->bedrooms }} bed
                                            @endif
                                            @if($house->bathrooms)
                                                · {{ $house->bathrooms }} bath
                                            @endif
                                        </small>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="fs-13 text-muted">{{ ucfirst($house->property_type) }}</td>
                        <td class="fs-13 text-muted">{{ ucfirst($house->listing_type) }}</td>
                        <td>
                            <span class="fw-bold">₦{{ number_format($house->price, 2) }}</span>
                        </td>
                        <td class="fs-13 text-muted">
                            {{ $house->city }}@if($house->city && $house->state), @endif{{ $house->state }}
                        </td>
                        <td>
                            <span class="badge orderer-badge badge-{{ $house->status }}">
                                {{ ucfirst($house->status) }}
                            </span>
                            @if($house->status === 'rejected' && $house->rejection_reason)
                                <p class="fs-11 text-danger mb-0 mt-1">
                                    {{ Str::limit($house->rejection_reason, 40) }}
                                </p>
                            @endif
                        </td> 
                        <td class="text-muted fs-12">{{ $house->created_at->format('M d, Y') }}</td>
                        <td>
                           
                            <div class="d-flex gap-2">
                                <a href="{{ route('seller.houses.show', $house->id) }}"
                                   class="btn btn-sm btn-outline-primary">
                                    <i class="feather-eye"></i>
                                </a>
                                <form action="{{ route('seller.houses.destroy', $house->id) }}"
                                      method="POST"
                                      onsubmit="return confirm('Delete this property listing permanently?')">
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
        <div class="p-3">{{ $houses->links() }}</div>
        @else
        <div class="text-center py-5 text-muted">
            <i class="feather-home mb-3 d-block" style="font-size:40px;"></i>
            <p class="mb-3">No properties listed yet.</p>
        </div>
        @endif
    </div>
</div>

@endsection