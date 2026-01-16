@extends('layouts.company')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="mb-0">Invoices & Payments</h4>
        <div class="d-flex gap-2">
            <div class="input-group" style="width: 250px;">
                <span class="input-group-text">
                    <i class="fas fa-calendar"></i>
                </span>
                <input type="month" class="form-control" id="monthFilter" 
                       value="{{ request('month', date('Y-m')) }}" onchange="filterByMonth(this.value)">
            </div>
            <select class="form-select" style="width: 150px;" id="statusFilter" onchange="filterByStatus(this.value)">
                <option value="">All Status</option>
                <option value="succeeded" {{ request('status') == 'succeeded' ? 'selected' : '' }}>Paid</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Failed</option>
            </select>
        </div>
    </div>
    <div class="card-body">
        @if(session('info'))
        <div class="alert alert-info">{{ session('info') }}</div>
        @endif
        
        <!-- Summary Cards -->
        <div class="row mb-4">
            <div class="col-md-3 mb-3">
                <div class="card text-center border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Total Paid
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            ${{ number_format($stats['total_paid'] ?? 0, 2) }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card text-center border-left-warning shadow h-100 py-2">
                    <div class="card-body">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            Pending
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            ${{ number_format($stats['total_pending'] ?? 0, 2) }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card text-center border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            This Month
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            ${{ number_format($stats['this_month'] ?? 0, 2) }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card text-center border-left-info shadow h-100 py-2">
                    <div class="card-body">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            Total Invoices
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ $stats['total_count'] ?? 0 }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        @if($payments->count() > 0)
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Invoice #</th>
                        <th>Date</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Description</th>
                        <th>Payment Method</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($payments as $payment)
                    <tr>
                        <td>
                            <strong>INV-{{ str_pad($payment->id, 6, '0', STR_PAD_LEFT) }}</strong>
                        </td>
                        <td>{{ $payment->created_at->format('Y-m-d') }}</td>
                        <td><strong>${{ number_format($payment->amount, 2) }}</strong></td>
                        <td>
                            <span class="badge bg-{{ 
                                $payment->status == 'succeeded' ? 'success' : 
                                ($payment->status == 'pending' ? 'warning' : 'danger') 
                            }}">
                                {{ ucfirst($payment->status) }}
                            </span>
                        </td>
                        <td>
                            @if($payment->subscription)
                                {{ $payment->subscription->plan->name }} Plan
                                @if($payment->subscription->billing_cycle)
                                    <small class="text-muted">({{ $payment->subscription->billing_cycle }})</small>
                                @endif
                            @else
                                Manual Payment
                            @endif
                        </td>
                        <td>
                            @if($payment->payment_method)
                                <i class="fab fa-cc-{{ strtolower($payment->payment_method) }} me-1"></i>
                                {{ ucfirst($payment->payment_method) }}
                                @if($payment->last_four)
                                    •••• {{ $payment->last_four }}
                                @endif
                            @else
                                <span class="text-muted">N/A</span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex gap-2">
                                <a href="{{ route('company.invoice.download', $payment) }}" 
                                   class="btn btn-sm btn-primary" title="Download Invoice">
                                    <i class="fas fa-download"></i>
                                </a>
                                <a href="{{ route('company.invoice.view', $payment) }}" 
                                   class="btn btn-sm btn-outline-primary" title="View Details">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            {{ $payments->links() }}
            
            <!-- Legend -->
            <div class="mt-4">
                <div class="alert alert-light">
                    <h6 class="mb-2">Invoice Status Legend:</h6>
                    <div class="d-flex gap-3">
                        <span class="badge bg-success">Paid/Succeeded</span>
                        <span class="badge bg-warning">Pending</span>
                        <span class="badge bg-danger">Failed/Refunded</span>
                    </div>
                </div>
            </div>
        </div>
        @else
        <div class="text-center py-5">
            <i class="fas fa-file-invoice fa-3x text-muted mb-3"></i>
            <h4>No Invoices Found</h4>
            <p class="text-muted">Your payment history will appear here.</p>
            @if(request('month') || request('status'))
                <a href="{{ route('company.invoices') }}" class="btn btn-primary mt-2">
                    Clear Filters
                </a>
            @endif
        </div>
        @endif
    </div>
</div>

<script>
function filterByMonth(month) {
    const url = new URL(window.location.href);
    url.searchParams.set('month', month);
    window.location.href = url.toString();
}

function filterByStatus(status) {
    const url = new URL(window.location.href);
    if (status) {
        url.searchParams.set('status', status);
    } else {
        url.searchParams.delete('status');
    }
    window.location.href = url.toString();
}

// Initialize filters from URL
document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const month = urlParams.get('month');
    const status = urlParams.get('status');
    
    if (month) {
        document.getElementById('monthFilter').value = month;
    }
    if (status) {
        document.getElementById('statusFilter').value = status;
    }
});
</script>
@endsection