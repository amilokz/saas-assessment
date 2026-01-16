@extends('layouts.super-admin')

@section('title', 'Platform Audit Logs')

@section('content')
<div class="container-fluid px-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Platform Audit Logs</h1>
            <p class="text-muted mb-0">Track all activities across all companies</p>
        </div>
        <div>
            <button class="btn btn-primary" onclick="exportAuditLogs()">
                <i class="fas fa-download me-2"></i> Export Logs
            </button>
        </div>
    </div>

    <!-- Filters -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filter Logs</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('super-admin.audit-logs.index') }}">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Company</label>
                        <select name="company_id" class="form-select">
                            <option value="">All Companies</option>
                            @foreach($companies as $company)
                                <option value="{{ $company->id }}" {{ request('company_id') == $company->id ? 'selected' : '' }}>
                                    {{ $company->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Event Type</label>
                        <select name="event" class="form-select">
                            <option value="">All Events</option>
                            <option value="created" {{ request('event') == 'created' ? 'selected' : '' }}>Created</option>
                            <option value="updated" {{ request('event') == 'updated' ? 'selected' : '' }}>Updated</option>
                            <option value="deleted" {{ request('event') == 'deleted' ? 'selected' : '' }}>Deleted</option>
                            <option value="approved" {{ request('event') == 'approved' ? 'selected' : '' }}>Approved</option>
                            <option value="rejected" {{ request('event') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                            <option value="suspended" {{ request('event') == 'suspended' ? 'selected' : '' }}>Suspended</option>
                            <option value="subscription" {{ request('event') == 'subscription' ? 'selected' : '' }}>Subscription</option>
                            <option value="payment" {{ request('event') == 'payment' ? 'selected' : '' }}>Payment</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">User</label>
                        <select name="user_id" class="form-select">
                            <option value="">All Users</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">From Date</label>
                        <input type="date" name="date_from" class="form-control" 
                               value="{{ request('date_from') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">To Date</label>
                        <input type="date" name="date_to" class="form-control" 
                               value="{{ request('date_to') }}">
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-12 d-flex justify-content-end gap-2">
                        <a href="{{ route('super-admin.audit-logs.index') }}" class="btn btn-secondary">
                            Clear Filters
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-filter me-1"></i> Apply Filters
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Audit Logs Table -->
    <div class="card shadow">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">
                All Activity Logs ({{ $logs->total() }})
            </h6>
            <div class="text-muted">
                Showing {{ $logs->firstItem() }} to {{ $logs->lastItem() }} of {{ $logs->total() }}
            </div>
        </div>
        <div class="card-body">
            @if($logs->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="thead-light">
                            <tr>
                                <th>TIME</th>
                                <th>EVENT</th>
                                <th>USER</th>
                                <th>COMPANY</th>
                                <th>DESCRIPTION</th>
                                <th>IP ADDRESS</th>
                                <th>DETAILS</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($logs as $log)
                            <tr>
                                <td>
                                    <div class="d-flex flex-column">
                                        <span class="small">{{ $log->created_at->format('M d, Y') }}</span>
                                        <span class="text-muted smaller">{{ $log->created_at->format('h:i A') }}</span>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-{{ 
                                        str_contains($log->event, 'create') ? 'success' : 
                                        (str_contains($log->event, 'update') ? 'info' : 
                                        (str_contains($log->event, 'delete') ? 'danger' : 'warning'))
                                    }}">
                                        {{ ucwords(str_replace('_', ' ', $log->event)) }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-xs me-2">
                                            <div class="avatar-title bg-light rounded-circle">
                                                <i class="fas fa-user text-muted"></i>
                                            </div>
                                        </div>
                                        <div>
                                            <div class="font-weight-bold">{{ $log->user->name ?? 'System' }}</div>
                                            <div class="text-muted small">{{ $log->user->email ?? 'system' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if($log->company)
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-xs me-2">
                                                <div class="avatar-title bg-light text-primary rounded-circle">
                                                    <i class="fas fa-building"></i>
                                                </div>
                                            </div>
                                            <div>
                                                <div class="font-weight-bold">{{ $log->company->name }}</div>
                                                <div class="text-muted small">{{ $log->company->email }}</div>
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="text-wrap" style="max-width: 300px;">
                                        {{ $log->description ?? 'No description' }}
                                        @if($log->model_type && $log->model_id)
                                            <div class="text-muted small mt-1">
                                                {{ class_basename($log->model_type) }} #{{ $log->model_id }}
                                            </div>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark">
                                        {{ $log->ip_address ?? 'N/A' }}
                                    </span>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-outline-primary" 
                                            data-bs-toggle="modal" data-bs-target="#logModal{{ $log->id }}">
                                        <i class="fas fa-search"></i> View
                                    </button>
                                </td>
                            </tr>

                            <!-- Modal for log details -->
                            <div class="modal fade" id="logModal{{ $log->id }}" tabindex="-1">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Audit Log Details</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <h6>Event Information</h6>
                                                    <table class="table table-sm">
                                                        <tr>
                                                            <th>Event:</th>
                                                            <td>
                                                                <span class="badge bg-{{ 
                                                                    str_contains($log->event, 'create') ? 'success' : 
                                                                    (str_contains($log->event, 'update') ? 'info' : 
                                                                    (str_contains($log->event, 'delete') ? 'danger' : 'warning'))
                                                                }}">
                                                                    {{ ucwords(str_replace('_', ' ', $log->event)) }}
                                                                </span>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th>Timestamp:</th>
                                                            <td>{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
                                                        </tr>
                                                        <tr>
                                                            <th>IP Address:</th>
                                                            <td>{{ $log->ip_address ?? 'N/A' }}</td>
                                                        </tr>
                                                        <tr>
                                                            <th>User Agent:</th>
                                                            <td><small>{{ $log->user_agent ?? 'N/A' }}</small></td>
                                                        </tr>
                                                    </table>
                                                </div>
                                                <div class="col-md-6">
                                                    <h6>User Information</h6>
                                                    <table class="table table-sm">
                                                        <tr>
                                                            <th>User:</th>
                                                            <td>{{ $log->user->name ?? 'System' }}</td>
                                                        </tr>
                                                        <tr>
                                                            <th>Email:</th>
                                                            <td>{{ $log->user->email ?? 'system' }}</td>
                                                        </tr>
                                                        <tr>
                                                            <th>Role:</th>
                                                            <td>
                                                                @if($log->user)
                                                                    {{ $log->user->isSuperAdmin() ? 'Super Admin' : 
                                                                      ($log->user->isCompanyAdmin() ? 'Company Admin' : 
                                                                      ($log->user->isSupportUser() ? 'Support User' : 'Normal User')) }}
                                                                @else
                                                                    System
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </div>
                                            </div>
                                            <div class="row mt-3">
                                                <div class="col-12">
                                                    <h6>Description & Details</h6>
                                                    <div class="card">
                                                        <div class="card-body">
                                                            <p>{{ $log->description ?? 'No description available' }}</p>
                                                            @if($log->properties)
                                                                <h6 class="mt-3">Additional Data:</h6>
                                                                <pre class="bg-light p-3 rounded"><code>{{ json_encode(json_decode($log->properties), JSON_PRETTY_PRINT) }}</code></pre>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-center mt-4">
                    {{ $logs->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-clipboard-list fa-4x text-muted mb-4"></i>
                    <h4 class="text-muted">No Audit Logs Found</h4>
                    <p class="text-muted mb-4">No activities have been logged yet.</p>
                    @if(request()->anyFilled(['company_id', 'event', 'user_id', 'date_from', 'date_to']))
                        <a href="{{ route('super-admin.audit-logs.index') }}" class="btn btn-primary">
                            Clear Filters
                        </a>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>

<script>
function exportAuditLogs() {
    // Build export URL with current filters
    const params = new URLSearchParams(window.location.search);
    params.set('export', 'csv');
    window.location.href = '{{ route('super-admin.audit-logs.index') }}?' + params.toString();
}
</script>
@endsection