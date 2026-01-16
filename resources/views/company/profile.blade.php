@extends('layouts.company')

@section('content')
<div class="card">
    <div class="card-header">
        <h4 class="mb-0">Company Profile</h4>
    </div>
    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
        
        @if(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif
        
        <div class="row">
            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-body text-center">
                        <div class="mb-3">
                            <i class="fas fa-building fa-4x text-primary"></i>
                        </div>
                        <h5>{{ $company->name }}</h5>
                        <p class="text-muted">Company</p>
                        <span class="badge bg-{{ 
                            $company->status == 'approved' ? 'success' : 
                            ($company->status == 'trial_pending_approval' ? 'warning' : 
                            ($company->status == 'pending' ? 'info' : 'danger')) 
                        }}">
                            {{ ucfirst(str_replace('_', ' ', $company->status)) }}
                        </span>
                    </div>
                </div>
            </div>
            
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Company Details</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-striped">
                            <tr>
                                <th width="30%">Company Name</th>
                                <td>{{ $company->name }}</td>
                            </tr>
                            <tr>
                                <th>Email</th>
                                <td>{{ $company->email }}</td>
                            </tr>
                            <tr>
                                <th>Phone</th>
                                <td>{{ $company->phone ?? 'Not set' }}</td>
                            </tr>
                            <tr>
                                <th>Address</th>
                                <td>{{ $company->address ?? 'Not set' }}</td>
                            </tr>
                            <tr>
                                <th>City</th>
                                <td>{{ $company->city ?? 'Not set' }}</td>
                            </tr>
                            <tr>
                                <th>State/Province</th>
                                <td>{{ $company->state ?? 'Not set' }}</td>
                            </tr>
                            <tr>
                                <th>Country</th>
                                <td>{{ $company->country ?? 'Not set' }}</td>
                            </tr>
                            <tr>
                                <th>Postal Code</th>
                                <td>{{ $company->postal_code ?? 'Not set' }}</td>
                            </tr>
                            <tr>
                                <th>Created</th>
                                <td>{{ $company->created_at->format('F j, Y') }}</td>
                            </tr>
                            <tr>
                                <th>Last Updated</th>
                                <td>{{ $company->updated_at->format('F j, Y') }}</td>
                            </tr>
                        </table>
                        
                        <div class="mt-3">
                            @if(Auth::user()->isCompanyAdmin())
                                <a href="{{ route('company.profile.edit') }}" class="btn btn-primary">
                                    <i class="fas fa-edit"></i> Edit Profile
                                </a>
                            @endif
                            <a href="{{ route('company.dashboard') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back to Dashboard
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection