<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'SaaS Platform') - {{ Auth::user()->company->name ?? 'Company' }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { padding-top: 20px; background-color: #f8f9fa; }
        .navbar-brand { font-weight: bold; }
        .sidebar { background-color: #343a40; color: white; min-height: calc(100vh - 56px); }
        .sidebar .nav-link { color: rgba(255,255,255,.8); }
        .sidebar .nav-link:hover { color: white; background-color: rgba(255,255,255,.1); }
        .sidebar .nav-link.active { color: white; background-color: #007bff; }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
        <div class="container">
            <a class="navbar-brand" href="{{ Auth::user()->isSuperAdmin() ? route('super-admin.dashboard') : route('company.dashboard') }}">
                SaaS Platform - {{ Auth::user()->isSuperAdmin() ? 'Super Admin' : (Auth::user()->company->name ?? 'Company') }}
            </a>
            <div class="navbar-nav ms-auto">
                <div class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                        <i class="fas fa-user"></i> {{ Auth::user()->name }}
                    </a>
                    <ul class="dropdown-menu">
                        <!-- FIXED: Check if user is super admin or company user -->
                        <li>
                            <a class="dropdown-item" href="{{ Auth::user()->isSuperAdmin() ? route('profile.edit') : route('company.profile') }}">
                                Profile
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item">Logout</button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>
    
    <!-- FIXED: Only show sidebar for COMPANY USERS, not for Super Admin -->
    @if(!Auth::user()->isSuperAdmin())
    <div class="container">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 mb-4">
                <div class="card sidebar">
                    <div class="card-body">
                        <nav class="nav flex-column">
                            <a class="nav-link {{ request()->routeIs('company.dashboard') ? 'active' : '' }}" 
                               href="{{ route('company.dashboard') }}">
                                <i class="fas fa-tachometer-alt"></i> Dashboard
                            </a>
                            <a class="nav-link {{ request()->routeIs('company.profile*') ? 'active' : '' }}" 
                               href="{{ route('company.profile') }}">
                                <i class="fas fa-building"></i> Company Profile
                            </a>
                            <a class="nav-link {{ request()->routeIs('company.files*') ? 'active' : '' }}" 
                               href="{{ route('company.files') }}">
                                <i class="fas fa-folder"></i> Files
                            </a>
                            <a class="nav-link {{ request()->routeIs('company.support*') ? 'active' : '' }}" 
                               href="{{ route('company.support') }}">
                                <i class="fas fa-headset"></i> Support
                            </a>
                            @if(Auth::user()->isCompanyAdmin())
                            <a class="nav-link {{ request()->routeIs('company.team*') ? 'active' : '' }}" 
                               href="{{ route('company.team') }}">
                                <i class="fas fa-users"></i> Team
                            </a>
                            <a class="nav-link {{ request()->routeIs('company.subscription*') ? 'active' : '' }}" 
                               href="{{ route('company.subscription') }}">
                                <i class="fas fa-credit-card"></i> Subscription
                            </a>
                            @endif
                        </nav>
                    </div>
                </div>
            </div>
            
            <!-- Main Content -->
            <div class="col-md-9">
                @yield('content')
            </div>
        </div>
    </div>
    @else
    <!-- For Super Admin: Full width content -->
    <div class="container">
        @yield('content')
    </div>
    @endif
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>