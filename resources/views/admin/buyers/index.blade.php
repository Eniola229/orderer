@extends('layouts.admin')
@section('title', 'Buyers')
@section('page_title', 'Buyer Management')
@section('breadcrumb')
    <li class="breadcrumb-item active">Buyers</li>
@endsection

@section('content')

<form action="{{ route('admin.buyers.index') }}" method="GET"
      class="d-flex gap-2 mb-4">
    <input type="text" name="search" class="form-control form-control-sm"
           placeholder="Search name or email..." value="{{ request('search') }}"
           style="width:280px;">
    <button type="submit" class="btn btn-sm btn-outline-primary">
        <i class="feather-search"></i>
    </button>
</form>

<div class="card">
    <div class="card-body p-0">
        @if($buyers->count())
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    亚
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Buyer</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Email</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Phone</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Orders</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Status</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Joined</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($buyers as $buyer)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                @if($buyer->avatar)
                                <img src="{{ $buyer->avatar }}"
                                     style="width:36px;height:36px;border-radius:50%;object-fit:cover;" alt="">
                                @else
                                <div style="width:36px;height:36px;border-radius:50%;background:#2ECC71;color:#fff;display:flex;align-items:center;justify-content:center;font-size:14px;font-weight:700;flex-shrink:0;">
                                    {{ strtoupper(substr($buyer->first_name,0,1)) }}
                                </div>
                                @endif
                                <strong class="fs-13">
                                    {{ $buyer->first_name }} {{ $buyer->last_name }}
                                </strong>
                            </div>
                        </td>
                        <td class="fs-13 text-muted">{{ $buyer->email }}</td>
                        <td class="fs-13 text-muted">{{ $buyer->phone ?? '—' }}</td>
                        <td class="fw-semibold">{{ $buyer->orders_count }}</td>
                        <td>
                            @if($buyer->is_active == 1)
                                <span class="badge orderer-badge badge-approved">Active</span>
                            @else
                                <span class="badge orderer-badge badge-rejected">Suspended</span>
                            @endif
                        </td>
                        <td class="text-muted fs-12">{{ $buyer->created_at->format('M d, Y') }}</td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('admin.buyers.show', $buyer->id) }}"
                                   class="btn btn-sm btn-outline-primary">View</a>
                                @if(auth('admin')->user()->canModerateSellers())
                                    @if($buyer->is_active == 1)
                                    <form action="{{ route('admin.buyers.suspend', $buyer->id) }}"
                                          method="POST"
                                          onsubmit="return confirm('Suspend this buyer?')">
                                        @csrf @method('PUT')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            Suspend
                                        </button>
                                    </form>
                                    @else
                                    <form action="{{ route('admin.buyers.unsuspend', $buyer->id) }}"
                                          method="POST">
                                        @csrf @method('PUT')
                                        <button type="submit" class="btn btn-sm btn-outline-success">
                                            Reinstate
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
        <div class="p-3">{{ $buyers->links() }}</div>
        @else
        <div class="text-center py-5 text-muted">
            <i class="feather-users mb-2 d-block" style="font-size:40px;"></i>
            <p>No buyers found.</p>
        </div>
        @endif
    </div>
</div>

@endsection