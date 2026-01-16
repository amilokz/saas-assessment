<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
        <div class="container">
            <a class="navbar-brand" href="#">
                @if(auth()->user()->isSuperAdmin())
                SaaS Platform - Super Admin
                @else
                SaaS Platform - {{ auth()->user()->company->name ?? 'User' }}
                @endif
            </a>
            <div class="navbar-nav ms-auto">
                @if(auth()->user()->isSuperAdmin())
                <a href="{{ route('super-admin.dashboard') }}" class="nav-link me-3">Dashboard</a>
                @else
                <a href="{{ route('company.dashboard') }}" class="nav-link me-3">Dashboard</a>
                @endif
                <form method="POST" action="{{ route('logout') }}" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-outline-light">Logout</button>
                </form>
            </div>
        </div>
    </nav>
    
    <div class="container mt-4">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0">User Profile</h4>
            </div>
            <div class="card-body">
                @if(session('status'))
                <div class="alert alert-success">{{ session('status') }}</div>
                @endif
                
                @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Name</label>
                            <input type="text" class="form-control" value="{{ auth()->user()->name }}" readonly>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" value="{{ auth()->user()->email }}" readonly>
                        </div>
                    </div>
                </div>
                
                @if(auth()->user()->isSuperAdmin())
                <div class="alert alert-info">
                    <h6>Super Admin Profile</h6>
                    <p class="mb-0">
                        You are logged in as a Super Administrator. 
                        Your profile management is handled separately from company profiles.
                    </p>
                </div>
                <a href="{{ route('super-admin.dashboard') }}" class="btn btn-primary">Back to Dashboard</a>
                
                @elseif(auth()->user()->company)
                <div class="alert alert-info">
                    <h6>Company User Profile</h6>
                    <p class="mb-0">
                        You are part of <strong>{{ auth()->user()->company->name }}</strong>.
                        For full company profile management, visit the company profile page.
                    </p>
                </div>
                <a href="{{ route('company.profile') }}" class="btn btn-primary">Go to Company Profile</a>
                <a href="{{ route('company.dashboard') }}" class="btn btn-secondary">Back to Dashboard</a>
                
                @else
                <div class="alert alert-warning">
                    <h6>No Company Associated</h6>
                    <p class="mb-0">Your account is not associated with any company.</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</body>
</html>