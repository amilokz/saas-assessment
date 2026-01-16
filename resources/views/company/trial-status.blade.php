<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trial Status - {{ $company->name }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-warning">
                        <h4 class="mb-0">Your Trial Status</h4>
                    </div>
                    <div class="card-body text-center">
                        <div class="alert alert-info">
                            <h5 class="alert-heading">Welcome to {{ config('app.name') }}!</h5>
                            <p>Your company <strong>{{ $company->name }}</strong> is currently on a 7-day free trial.</p>
                        </div>
                        
                        <div class="row mt-4">
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-body">
                                        <h5>Trial Days Remaining</h5>
                                        <div class="display-4 text-primary">{{ $trialDaysLeft }}</div>
                                        <p class="text-muted">days</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-body">
                                        <h5>Trial End Date</h5>
                                        <div class="h4">{{ $company->trial_ends_at->format('F d, Y') }}</div>
                                        <p class="text-muted">{{ $company->trial_ends_at->format('h:i A') }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="card mt-4">
                            <div class="card-header">
                                <h5 class="mb-0">Trial Limitations</h5>
                            </div>
                            <div class="card-body text-start">
                                <ul>
                                    <li>Maximum 1 user (including you)</li>
                                    <li>Maximum 2 file uploads</li>
                                    <li>Limited support messages</li>
                                    <li>No paid subscription access yet</li>
                                </ul>
                            </div>
                        </div>
                        
                        <div class="mt-4">
                            <div class="alert alert-warning">
                                <h6>Important:</h6>
                                <p class="mb-0">
                                    Your account needs approval from the Super Admin before you can subscribe to paid plans.
                                    You'll receive an email once your company is approved.
                                </p>
                            </div>
                        </div>
                        
                        <div class="mt-4">
                            <a href="{{ route('company.dashboard') }}" class="btn btn-primary btn-lg">
                                Go to Dashboard
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>