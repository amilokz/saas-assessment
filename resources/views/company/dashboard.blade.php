@extends('layouts.company') {{-- This uses the fixed company layout --}}

@section('content')
<div class="card">
    <div class="card-header">
        <h4 class="mb-0">Welcome, {{ $user->name }}!</h4>
    </div>
    <div class="card-body">
        <div class="alert alert-success">
            <h5>Company: {{ $company->name }}</h5>
            <p class="mb-0">Status: <strong>{{ ucfirst(str_replace('_', ' ', $company->status)) }}</strong></p>
        </div>
        
        @if($company->status === 'trial_pending_approval')
        <div class="alert alert-warning">
            <h5>Trial Mode Active</h5>
            <p class="mb-0">
                Your company is on a 7-day free trial. 
                <a href="{{ route('company.trial-status') }}" class="alert-link">View trial status</a>
            </p>
        </div>
        @endif
        
        <div class="row mt-4">
            <div class="col-md-4">
                <div class="card text-center">
                    <div class="card-body">
                        <h5>Files</h5>
                        <h2>0</h2>
                        <a href="{{ route('company.files') }}" class="btn btn-sm btn-primary">Manage Files</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-center">
                    <div class="card-body">
                        <h5>Team Members</h5>
                        <h2>1</h2>
                        @if($user->isCompanyAdmin())
                        <a href="{{ route('company.team') }}" class="btn btn-sm btn-primary">Manage Team</a>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-center">
                    <div class="card-body">
                        <h5>Support Tickets</h5>
                        <h2>0</h2>
                        <a href="{{ route('company.support') }}" class="btn btn-sm btn-primary">View Support</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection