@extends('layouts.super-admin')

@section('title', 'Manage Companies')

@section('content')
<div class="container-fluid px-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Company Management</h1>
            <p class="text-muted mb-0">Review, approve, and manage registered companies</p>
        </div>
        <div class="d-flex">
            <form method="GET" action="{{ route('super-admin.companies.index') }}" class="d-flex me-2">
                <div class="input-group" style="width: 300px;">
                    <span class="input-group-text">
                        <i class="fas fa-search"></i>
                    </span>
                    <input type="text" class="form-control" name="search" 
                           placeholder="Search companies..." value="{{ request('search') }}">
                    <button type="submit" class="btn btn-primary">Search</button>
                </div>
            </form>
            <button class="btn btn-primary" onclick="exportCompanies()">
                <i class="fas fa-download me-2"></i> Export
            </button>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        @php
            $statusStats = [
                'pending' => ['color' => 'warning', 'icon' => 'clock', 'label' => 'Pending'],
                'trial_pending_approval' => ['color' => 'info', 'icon' => 'hourglass-half', 'label' => 'On Trial'],
                'approved' => ['color' => 'success', 'icon' => 'check-circle', 'label' => 'Active'],
                'rejected' => ['color' => 'danger', 'icon' => 'times-circle', 'label' => 'Rejected'],
                'suspended' => ['color' => 'secondary', 'icon' => 'pause-circle', 'label' => 'Suspended'],
            ];
        @endphp

        @foreach($statusStats as $status => $config)
            @php
                $count = $companies->where('status', $status)->count();
            @endphp
            <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
                <div class="card border-left-{{ $config['color'] }} shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-{{ $config['color'] }} text-uppercase mb-1">
                                    {{ $config['label'] }}
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $count }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-{{ $config['icon'] }} fa-2x text-{{ $config['color'] }}"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Companies Table -->
    <div class="card shadow">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">
                All Companies ({{ $companies->total() }})
            </h6>
            <div class="dropdown">
                <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <i class="fas fa-filter me-1"></i> Filter by Status
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="{{ route('super-admin.companies.index') }}">All Companies</a></li>
                    <li><a class="dropdown-item" href="{{ route('super-admin.companies.index', ['status' => 'pending']) }}">Pending Approval</a></li>
                    <li><a class="dropdown-item" href="{{ route('super-admin.companies.index', ['status' => 'approved']) }}">Active Companies</a></li>
                    <li><a class="dropdown-item" href="{{ route('super-admin.companies.index', ['status' => 'trial_pending_approval']) }}">On Trial</a></li>
                    <li><a class="dropdown-item" href="{{ route('super-admin.companies.index', ['status' => 'suspended']) }}">Suspended</a></li>
                    <li><a class="dropdown-item" href="{{ route('super-admin.companies.index', ['status' => 'rejected']) }}">Rejected</a></li>
                </ul>
            </div>
        </div>
        <div class="card-body">
            @if($companies->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="thead-light">
                            <tr>
                                <th>COMPANY</th>
                                <th>ADMIN</th>
                                <th>USERS</th>
                                <th>STATUS</th>
                                <th>REGISTERED</th>
                                <th>ACTIONS</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($companies as $company)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0">
                                            <div class="avatar-sm">
                                                <div class="avatar-title bg-light text-primary rounded-circle">
                                                    <i class="fas fa-building"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <a href="{{ route('super-admin.companies.show', $company) }}" class="text-decoration-none">
                                                <h6 class="mb-1">{{ $company->name }}</h6>
                                            </a>
                                            <p class="text-muted mb-0 small">{{ $company->email }}</p>
                                            @if($company->business_type)
                                                <span class="badge bg-light text-dark">{{ $company->business_type }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0">
                                            <div class="avatar-xs">
                                                <div class="avatar-title bg-light rounded-circle">
                                                    <i class="fas fa-user text-muted"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1 ms-2">
                                            <span class="d-block">{{ $company->admin_name }}</span>
                                            <small class="text-muted">{{ $company->admin_email }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark">
                                        <i class="fas fa-users me-1"></i> {{ $company->users_count ?? 0 }}
                                    </span>
                                </td>
                                <td>
                                    @php
                                        $statusConfig = [
                                            'pending' => ['color' => 'warning', 'icon' => 'clock'],
                                            'trial_pending_approval' => ['color' => 'info', 'icon' => 'hourglass-half'],
                                            'approved' => ['color' => 'success', 'icon' => 'check-circle'],
                                            'rejected' => ['color' => 'danger', 'icon' => 'times-circle'],
                                            'suspended' => ['color' => 'secondary', 'icon' => 'pause-circle'],
                                        ][$company->status];
                                    @endphp
                                    <span class="badge bg-{{ $statusConfig['color'] }}">
                                        <i class="fas fa-{{ $statusConfig['icon'] }} me-1"></i>
                                        {{ str_replace('_', ' ', ucfirst($company->status)) }}
                                    </span>
                                    @if($company->trial_ends_at)
                                        <div class="text-xs text-muted mt-1">
                                            Trial ends: {{ $company->trial_ends_at->format('M d, Y') }}
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex flex-column">
                                        <span>{{ $company->created_at->format('M d, Y') }}</span>
                                        <small class="text-muted">{{ $company->created_at->diffForHumans() }}</small>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex gap-2">
                                        @if(in_array($company->status, ['pending', 'trial_pending_approval']))
                                            <form action="{{ route('super-admin.companies.approve', $company) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="btn btn-success btn-sm">
                                                    <i class="fas fa-check"></i> Approve
                                                </button>
                                            </form>
                                            <form action="{{ route('super-admin.companies.reject', $company) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="btn btn-danger btn-sm">
                                                    <i class="fas fa-times"></i> Reject
                                                </button>
                                            </form>
                                        @elseif($company->status === 'approved')
                                            <form action="{{ route('super-admin.companies.suspend', $company) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="btn btn-warning btn-sm">
                                                    <i class="fas fa-pause"></i> Suspend
                                                </button>
                                            </form>
                                            <a href="{{ route('super-admin.companies.show', $company) }}" class="btn btn-outline-primary btn-sm">
                                                <i class="fas fa-eye"></i> View
                                            </a>
                                        @elseif($company->status === 'suspended')
                                            <form action="{{ route('super-admin.companies.activate', $company) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="btn btn-success btn-sm">
                                                    <i class="fas fa-play"></i> Activate
                                                </button>
                                            </form>
                                            <a href="{{ route('super-admin.companies.show', $company) }}" class="btn btn-outline-primary btn-sm">
                                                <i class="fas fa-eye"></i> View
                                            </a>
                                        @else
                                            <a href="{{ route('super-admin.companies.show', $company) }}" class="btn btn-outline-primary btn-sm">
                                                <i class="fas fa-eye"></i> View
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-between align-items-center mt-4">
                    <div class="text-muted">
                        Showing {{ $companies->firstItem() }} to {{ $companies->lastItem() }} of {{ $companies->total() }} entries
                    </div>
                    <div>
                        {{ $companies->links() }}
                    </div>
                </div>
            @else
                <div class="text-center py-5">
                    <div class="py-5">
                        <i class="fas fa-building fa-4x text-muted mb-4"></i>
                        <h4 class="text-muted">No Companies Found</h4>
                        <p class="text-muted mb-4">No companies have registered yet.</p>
                        @if(request('search') || request('status'))
                            <a href="{{ route('super-admin.companies.index') }}" class="btn btn-primary">
                                Clear Filters
                            </a>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
function exportCompanies() {
    // Add export functionality here
    alert('Export feature would be implemented here');
}
</script>
@endsection