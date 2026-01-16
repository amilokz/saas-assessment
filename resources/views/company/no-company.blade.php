@extends('layouts.company')

@section('content')
<div class="card">
    <div class="card-header">
        <h4 class="mb-0">No Company Found</h4>
    </div>
    <div class="card-body text-center py-5">
        <div class="mb-4">
            <i class="fas fa-building fa-4x text-muted mb-3"></i>
            <h3>No Company Associated</h3>
        </div>
        
        <p class="text-muted mb-4">
            Your user account is not associated with any company. 
            This can happen if you were recently added to a company or if there's an issue with your account setup.
        </p>
        
        <div class="alert alert-warning">
            <strong>User Information:</strong><br>
            Name: {{ $user->name }}<br>
            Email: {{ $user->email }}<br>
            Role: {{ $user->isSuperAdmin() ? 'Super Admin' : 'Company User' }}
        </div>
        
        <div class="mt-4">
            @if($user->isSuperAdmin())
                <a href="{{ route('super-admin.dashboard') }}" class="btn btn-primary">
                    <i class="fas fa-arrow-left"></i> Go to Super Admin Dashboard
                </a>
            @else
                <a href="{{ route('company.dashboard') }}" class="btn btn-primary">
                    <i class="fas fa-arrow-left"></i> Back to Dashboard
                </a>
            @endif
            
            <button class="btn btn-outline-danger" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="fas fa-sign-out-alt"></i> Logout
            </button>
            
            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                @csrf
            </form>
        </div>
    </div>
</div>
@endsection