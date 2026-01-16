@extends('layouts.super-admin')

@section('title', 'Dashboard')

@section('content')
<div class="container-fluid px-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Dashboard Overview</h1>
            <p class="text-muted mb-0">Welcome back, {{ Auth::user()->name }}! Here's what's happening with your platform.</p>
        </div>
        <div class="dropdown">
            <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                <i class="fas fa-calendar-alt me-2"></i>Last 30 Days
            </button>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="#">Today</a></li>
                <li><a class="dropdown-item" href="#">Last 7 Days</a></li>
                <li><a class="dropdown-item active" href="#">Last 30 Days</a></li>
                <li><a class="dropdown-item" href="#">This Month</a></li>
                <li><a class="dropdown-item" href="#">Last Month</a></li>
            </ul>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Companies
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_companies'] }}</div>
                            <div class="mt-2">
                                <span class="badge bg-success">Active: {{ $stats['active_companies'] }}</span>
                                <span class="badge bg-warning">Trial: {{ $stats['trial_companies'] }}</span>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-building fa-2x text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Total Users
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_users'] }}</div>
                            <div class="mt-2">
                                <span class="text-xs text-muted">Across all companies</span>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Active Subscriptions
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['active_subscriptions'] }}</div>
                            <div class="mt-2">
                                <span class="text-xs text-muted">Ongoing subscriptions</span>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-credit-card fa-2x text-info"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Pending Approval
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['pending_companies'] }}</div>
                            <div class="mt-2">
                                <span class="text-xs text-muted">Awaiting review</span>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Companies & Quick Actions -->
    <div class="row">
        <!-- Recent Companies -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Recent Companies</h6>
                    <a href="{{ route('super-admin.companies.index') }}" class="btn btn-sm btn-primary">
                        View All <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Company Name</th>
                                    <th>Status</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentCompanies as $company)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm me-3">
                                                <div class="avatar-title bg-light text-primary rounded-circle">
                                                    <i class="fas fa-building"></i>
                                                </div>
                                            </div>
                                            <div>
                                                <h6 class="mb-0">{{ $company->name }}</h6>
                                                <small class="text-muted">{{ $company->email }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ 
                                            $company->status == 'approved' ? 'success' : 
                                            ($company->status == 'trial_pending_approval' ? 'warning' : 
                                            ($company->status == 'pending' ? 'info' : 'danger')) 
                                        }}">
                                            {{ str_replace('_', ' ', ucfirst($company->status)) }}
                                        </span>
                                    </td>
                                    <td>
                                        {{ $company->created_at->format('M d, Y') }}
                                        <div class="text-xs text-muted">{{ $company->created_at->diffForHumans() }}</div>
                                    </td>
                                    <td>
                                        <a href="#" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Quick Actions</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('super-admin.companies.index') }}" class="btn btn-primary btn-lg">
                            <i class="fas fa-building me-2"></i> Manage Companies
                        </a>
                        <a href="{{ route('super-admin.plans.index') }}" class="btn btn-outline-primary btn-lg">
                            <i class="fas fa-credit-card me-2"></i> Manage Plans
                        </a>
                        <a href="#" class="btn btn-outline-secondary btn-lg">
                            <i class="fas fa-chart-line me-2"></i> View Reports
                        </a>
                        <a href="#" class="btn btn-outline-secondary btn-lg">
                            <i class="fas fa-cog me-2"></i> Settings
                        </a>
                    </div>
                    
                    <hr class="my-4">
                    
                    <h6 class="font-weight-bold mb-3">Platform Status</h6>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">System Uptime</span>
                        <span class="text-success">99.9%</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">API Status</span>
                        <span class="text-success">Operational</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Last Backup</span>
                        <span class="text-muted">Today, 02:00 AM</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection