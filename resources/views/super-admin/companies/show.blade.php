@extends('layouts.super-admin')

@section('title', 'Company Details - ' . $company->name)

@section('content')
<div class="container-fluid px-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">{{ $company->name }}</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('super-admin.companies.index') }}">Companies</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $company->name }}</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2">
            @if(in_array($company->status, ['pending', 'trial_pending_approval']))
                <form action="{{ route('super-admin.companies.approve', $company) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-success btn-sm">
                        <i class="fas fa-check me-1"></i> Approve
                    </button>
                </form>
                <form action="{{ route('super-admin.companies.reject', $company) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-danger btn-sm">
                        <i class="fas fa-times me-1"></i> Reject
                    </button>
                </form>
            @elseif($company->status === 'approved')
                <form action="{{ route('super-admin.companies.suspend', $company) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-warning btn-sm">
                        <i class="fas fa-pause me-1"></i> Suspend
                    </button>
                </form>
            @elseif($company->status === 'suspended')
                <form action="{{ route('super-admin.companies.activate', $company) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-success btn-sm">
                        <i class="fas fa-play me-1"></i> Activate
                    </button>
                </form>
            @endif
            <a href="{{ route('super-admin.companies.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left me-1"></i> Back
            </a>
        </div>
    </div>

    <!-- Company Info Card -->
    <div class="row">
        <div class="col-xl-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Company Information</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-sm">
                                <tr>
                                    <th width="40%">Company Name:</th>
                                    <td>
                                        <strong>{{ $company->name }}</strong>
                                        @if($company->business_type)
                                            <span class="badge bg-light text-dark ms-2">{{ $company->business_type }}</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Email:</th>
                                    <td>{{ $company->email }}</td>
                                </tr>
                                <tr>
                                    <th>Status:</th>
                                    <td>
                                        <span class="badge bg-{{ 
                                            $company->status == 'approved' ? 'success' : 
                                            ($company->status == 'trial_pending_approval' ? 'warning' : 
                                            ($company->status == 'pending' ? 'info' : 'danger')) 
                                        }}">
                                            {{ str_replace('_', ' ', ucfirst($company->status)) }}
                                        </span>
                                        @if($company->trial_ends_at && $company->status == 'trial_pending_approval')
                                            <div class="text-xs text-muted mt-1">
                                                Trial ends: {{ $company->trial_ends_at->format('M d, Y') }}
                                                ({{ $company->trial_ends_at->diffForHumans() }})
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Registered:</th>
                                    <td>{{ $company->created_at->format('F d, Y H:i') }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-sm">
                                <tr>
                                    <th width="40%">Admin Name:</th>
                                    <td>{{ $company->admin_name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Admin Email:</th>
                                    <td>{{ $company->admin_email ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Phone:</th>
                                    <td>{{ $company->phone ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Address:</th>
                                    <td>
                                        @if($company->address || $company->city || $company->country)
                                            {{ $company->address ?? '' }}
                                            {{ $company->city ? ', ' . $company->city : '' }}
                                            {{ $company->country ? ', ' . $company->country : '' }}
                                            {{ $company->postal_code ? ' (' . $company->postal_code . ')' : '' }}
                                        @else
                                            <span class="text-muted">Not provided</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Team Members Section -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Team Members ({{ $company->users->count() }})</h6>
                </div>
                <div class="card-body">
                    @if($company->users->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Joined</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($company->users as $user)
                                <tr>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        <span class="badge bg-light text-dark">
                                            {{ $user->role->display_name ?? 'No role' }}
                                        </span>
                                    </td>
                                    <td>{{ $user->created_at->format('Y-m-d') }}</td>
                                    <td>
                                        <span class="badge bg-{{ $user->is_active ? 'success' : 'secondary' }}">
                                            {{ $user->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-4">
                        <i class="fas fa-users fa-2x text-muted mb-3"></i>
                        <p class="text-muted">No team members found</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Recent Activity</h6>
                </div>
                <div class="card-body">
                    @if($auditLogs->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Time</th>
                                    <th>User</th>
                                    <th>Event</th>
                                    <th>Description</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($auditLogs->take(5) as $log)
                                <tr>
                                    <td>
                                        <small>{{ $log->created_at->format('M d, H:i') }}</small>
                                    </td>
                                    <td>
                                        <small>{{ $log->user->name ?? 'System' }}</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ 
                                            str_contains($log->event, 'create') ? 'success' : 
                                            (str_contains($log->event, 'update') ? 'info' : 'danger')
                                        }}">
                                            {{ ucwords(str_replace('_', ' ', $log->event)) }}
                                        </span>
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ Str::limit($log->description, 50) }}</small>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if($auditLogs->count() > 5)
                    <div class="text-center mt-3">
                        <a href="#" class="btn btn-sm btn-outline-primary">View All Activities</a>
                    </div>
                    @endif
                    @else
                    <div class="text-center py-4">
                        <i class="fas fa-history fa-2x text-muted mb-3"></i>
                        <p class="text-muted">No recent activity</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Right Column -->
        <div class="col-xl-4">
            <!-- Quick Stats -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Quick Stats</h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6 mb-3">
                            <div class="border rounded p-3">
                                <div class="text-primary font-weight-bold h4">
                                    {{ $company->users->count() }}
                                </div>
                                <div class="text-muted small">Team Members</div>
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="border rounded p-3">
                                <div class="text-primary font-weight-bold h4">
                                    {{ $company->files->count() }}
                                </div>
                                <div class="text-muted small">Files</div>
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="border rounded p-3">
                                <div class="text-primary font-weight-bold h4">
                                    {{ $company->messages->count() }}
                                </div>
                                <div class="text-muted small">Support Tickets</div>
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="border rounded p-3">
                                <div class="text-primary font-weight-bold h4">
                                    {{ $company->subscriptions->count() }}
                                </div>
                                <div class="text-muted small">Subscriptions</div>
                            </div>
                        </div>
                    </div>

                    <!-- Storage Usage -->
                    @if(isset($totalStorageMB))
                    <div class="mt-4">
                        <h6 class="font-weight-bold mb-2">Storage Usage</h6>
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-sm">{{ $totalStorageMB }} MB used</span>
                            <span class="text-sm">{{ round(($totalStorageMB / 1024) * 100, 1) }}%</span>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar" role="progressbar" 
                                 style="width: {{ min(($totalStorageMB / 1024) * 100, 100) }}%"></div>
                        </div>
                        <div class="text-xs text-muted mt-1">
                            {{ $totalStorageMB }} MB of 1024 MB
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Current Subscription -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Current Subscription</h6>
                </div>
                <div class="card-body">
                    @if($currentSubscription)
                    <div class="text-center">
                        <h5 class="text-primary">{{ $currentSubscription->plan->name ?? 'Unknown Plan' }}</h5>
                        <p class="mb-1">
                            <strong>${{ number_format($currentSubscription->amount, 2) }}</strong>
                            / {{ $currentSubscription->billing_cycle }}
                        </p>
                        <p class="text-muted small mb-2">
                            Status: 
                            <span class="badge bg-{{ $currentSubscription->status == 'active' ? 'success' : 'warning' }}">
                                {{ ucfirst($currentSubscription->status) }}
                            </span>
                        </p>
                        <p class="text-muted small">
                            Started: {{ $currentSubscription->created_at->format('M d, Y') }}
                        </p>
                    </div>
                    @else
                    <div class="text-center py-3">
                        <i class="fas fa-credit-card fa-2x text-muted mb-3"></i>
                        <p class="text-muted">No active subscription</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Recent Files -->
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Recent Files</h6>
                </div>
                <div class="card-body">
                    @if($company->files->count() > 0)
                    <div class="list-group list-group-flush">
                        @foreach($company->files->take(5) as $file)
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0 py-2 border-0">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-file text-muted me-2"></i>
                                <div>
                                    <div class="text-sm">{{ Str::limit($file->original_name, 20) }}</div>
                                    <div class="text-xs text-muted">{{ round($file->size / 1024, 2) }} KB</div>
                                </div>
                            </div>
                            <small class="text-muted">{{ $file->created_at->format('M d') }}</small>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="text-center py-3">
                        <i class="fas fa-file fa-2x text-muted mb-3"></i>
                        <p class="text-muted">No files uploaded</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection