@extends('layouts.admin')
@section('title', 'Admins')
@section('page_title', 'Admin Accounts')
@section('breadcrumb')
    <li class="breadcrumb-item active">Admins</li>
@endsection
@section('page_actions')
    @if(auth('admin')->user()->canManageAdmins())
    <a href="{{ route('admin.admins.create') }}" class="btn btn-primary btn-sm">
        <i class="feather-plus me-1"></i> Add Admin
    </a>
    @endif
@endsection

@section('content')

@if(!auth('admin')->user()->canManageAdmins())
<div class="alert alert-danger">
    <i class="feather-lock me-2"></i>
    You do not have permission to manage admin accounts.
    Only <strong>Super Admin</strong> and <strong>HR</strong> can access this section.
</div>
@else

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                     <tr>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Admin</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Email</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Role</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Status</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Last Login</th>
                        <th class="fs-11 text-uppercase text-muted fw-semibold">Actions</th>
                     </tr>
                </thead>
                <tbody>
                    @foreach($admins as $admin)
                    <tr class="{{ $admin->id === auth('admin')->id() ? 'table-success' : '' }}">
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div style="width:36px;height:36px;border-radius:50%;background:#1a1a2e;color:#2ECC71;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:14px;flex-shrink:0;">
                                    {{ strtoupper(substr($admin->first_name,0,1)) }}
                                </div>
                                <div>
                                    <p class="mb-0 fw-semibold fs-13">
                                        {{ $admin->full_name }}
                                        @if($admin->id === auth('admin')->id())
                                        <span class="badge bg-success ms-1" style="font-size:10px;">You</span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </td>
                        <td class="fs-13 text-muted">{{ $admin->email }}</td>
                        <td>
                            @php
                                $roleColors = [
                                    'super_admin'       => '#1a1a2e',
                                    'finance_admin'     => '#27AE60',
                                    'support_admin'     => '#E74C3C',
                                    'content_moderator' => '#F39C12',
                                    'hr'                => '#2980B9',
                                ];
                                $color = $roleColors[$admin->role] ?? '#888';
                            @endphp
                            <span style="background:{{ $color }}20;color:{{ $color }};padding:3px 10px;border-radius:12px;font-size:11px;font-weight:700;">
                                {{ str_replace('_',' ',ucfirst($admin->role)) }}
                            </span>
                        </td>
                        <td>
                            @if($admin->is_active)
                                <span class="badge orderer-badge badge-approved">
                                    Active
                                </span>
                            @else
                                <span class="badge orderer-badge badge-rejected">
                                    Inactive
                                </span>
                            @endif
                        </td>
                        <td class="text-muted fs-12">
                            {{ $admin->last_login_at ? $admin->last_login_at->diffForHumans() : 'Never' }}
                        </td>
                        <td>
                            @if($admin->id !== auth('admin')->id())
                            <div class="d-flex gap-1">
                                <a href="{{ route('admin.admins.edit', $admin->id) }}"
                                   class="btn btn-sm btn-outline-primary">Edit</a>
                                @if($admin->is_active)
                                <form action="{{ route('admin.admins.suspend', $admin->id) }}"
                                      method="POST"
                                      onsubmit="return confirm('Suspend this admin?')">
                                    @csrf @method('PUT')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        Suspend
                                    </button>
                                </form>
                                @else
                                <form action="{{ route('admin.admins.activate', $admin->id) }}"
                                      method="POST"
                                      onsubmit="return confirm('Activate this admin?')">
                                    @csrf @method('PUT')
                                    <button type="submit" class="btn btn-sm btn-outline-success">
                                        Activate
                                    </button>
                                </form>
                                @endif
                            </div>
                            @else
                            <span class="text-muted fs-12">Current session</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="p-3">{{ $admins->links() }}</div>
    </div>
</div>

{{-- Role permissions reference --}}
<div class="card mt-4">
    <div class="card-header">
        <h5 class="card-title mb-0">Role Permissions Reference</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-sm align-middle mb-0">
                <thead class="table-light">
                     <tr>
                        <th class="fs-12">Permission</th>
                        <th class="fs-12 text-center">Super Admin</th>
                        <th class="fs-12 text-center">Finance</th>
                        <th class="fs-12 text-center">Support</th>
                        <th class="fs-12 text-center">Content Mod</th>
                        <th class="fs-12 text-center">HR</th>
                     </tr>
                </thead>
                <tbody class="fs-12">
                    @foreach([
                        'View Dashboard'        => [1,1,1,1,1],
                        'Approve Sellers'       => [1,0,0,1,0],
                        'Approve Products'      => [1,0,0,1,0],
                        'Manage Ads'            => [1,0,0,1,0],
                        'Manage Categories'     => [1,0,0,1,0],
                        'Handle Support'        => [1,0,1,0,0],
                        'View Finance'          => [1,1,0,0,0],
                        'Process Withdrawals'   => [1,1,0,0,0],
                        'Edit/Refund Orders'    => [1,1,0,0,0],
                        'Manage Admins'         => [1,0,0,0,1],
                        'View Activity Logs'    => [1,0,0,0,0],
                    ] as $perm => $roles)
                    <tr>
                        <td class="fw-semibold">{{ $perm }}</td>
                        @foreach($roles as $allowed)
                        <td class="text-center">
                            @if($allowed)
                            <i class="feather-check text-success" style="font-size:16px;"></i>
                            @else
                            <i class="feather-x text-muted" style="font-size:16px;opacity:.3;"></i>
                            @endif
                        </td>
                        @endforeach
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@endif

@endsection