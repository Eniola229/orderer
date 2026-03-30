@extends('layouts.seller')
@section('title', 'Services')
@section('page_title', 'My Services')
@section('breadcrumb')
    <li class="breadcrumb-item active">Services</li>
@endsection
@section('page_actions')
    <a href="{{ route('seller.services.create') }}" class="btn btn-primary btn-sm">
        <i class="feather-plus me-1"></i> Add Service
    </a>
@endsection

@section('content')

<style>
    .portfolio-images {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }
    .portfolio-images img {
        width: 80px;
        height: 80px;
        object-fit: cover;
        border-radius: 8px;
        border: 1px solid #e5e7eb;
        cursor: pointer;
    }
</style>

{{-- Status filter tabs --}}
<div class="d-flex gap-2 mb-3 flex-wrap">
    @foreach(['all','pending','approved','rejected','draft','suspended'] as $tab)
    <a href="{{ route('seller.services.index', ['status' => $tab]) }}"
       class="btn btn-sm {{ request('status','all') === $tab ? 'btn-primary' : 'btn-outline-secondary' }}">
        {{ ucfirst($tab) }}
    </a>
    @endforeach
</div>

<div class="card">
    <div class="card-body p-0">
        @if($services->count())
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    践
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Service</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Category</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Price</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Delivery</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Status</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Date</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Actions</th>
                     </thead>
                <tbody>
                    @foreach($services as $service)
                    <tr>
                        <td class="fw-semibold fs-13">
                            <p class="mb-0">{{ Str::limit($service->title, 45) }}</p>
                            @if($service->location)
                                <small class="text-muted">
                                    <i class="feather-map-pin me-1"></i>{{ $service->location }}
                                </small>
                            @endif
                          
                        
                        <td class="fs-13 text-muted">{{ $service->category->name ?? '—' }}  
                        
                        <td>
                            @if($service->pricing_type === 'negotiable')
                                <span class="text-muted fs-13">Negotiable</span>
                            @else
                                <span class="fw-bold text-success">
                                    ₦{{ number_format($service->price, 2) }}
                                    @if($service->pricing_type === 'hourly')
                                        <small class="text-muted fw-normal">/hr</small>
                                    @endif
                                </span>
                            @endif
                        
                        
                        <td class="fs-13 text-muted">{{ $service->delivery_time ?? '—' }}  
                        
                        <td>
                            <span class="badge orderer-badge badge-{{ $service->status }}">
                                {{ ucfirst($service->status) }}
                            </span>
                            @if($service->status === 'rejected' && $service->rejection_reason)
                                <p class="fs-11 text-danger mb-0 mt-1">
                                    {{ Str::limit($service->rejection_reason, 40) }}
                                </p>
                            @endif
                        
                        
                        <td class="text-muted fs-12">{{ $service->created_at->format('M d, Y') }}  
                        
                        <td>
                            <div class="d-flex gap-2">
                                <a href="{{ route('seller.services.show', $service->id) }}" 
                                   class="btn btn-sm btn-outline-primary">
                                    <i class="feather-eye"></i> View
                                </a>
                                <a href="{{ route('seller.services.edit', $service->id) }}" 
                                   class="btn btn-sm btn-outline-warning">
                                    <i class="feather-edit"></i>
                                </a>
                                <form action="{{ route('seller.services.destroy', $service->id) }}"
                                      method="POST"
                                      onsubmit="return confirm('Delete this service permanently?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        <i class="feather-trash-2"></i>
                                    </button>
                                </form>
                            </div>
                        
                      
                    </tr>
                    @endforeach
                </tbody>
             </table>
        </div>
        <div class="p-3">{{ $services->links() }}</div>
        @else
        <div class="text-center py-5 text-muted">
            <i class="feather-settings mb-3 d-block" style="font-size:40px;"></i>
            <p class="mb-3">No services found.</p>
        </div>
        @endif
    </div>
</div>

@endsection