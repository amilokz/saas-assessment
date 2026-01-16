@extends('layouts.company')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="mb-0">Support Tickets</h4>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newTicketModal">
            <i class="fas fa-plus"></i> New Ticket
        </button>
    </div>
    <div class="card-body">
        @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h5>Total</h5>
                        <h2>{{ $stats['total'] }}</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h5>Open</h5>
                        <h2 class="text-warning">{{ $stats['open'] }}</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h5>In Progress</h5>
                        <h2 class="text-info">{{ $stats['in_progress'] }}</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h5>Closed</h5>
                        <h2 class="text-success">{{ $stats['closed'] }}</h2>
                    </div>
                </div>
            </div>
        </div>
        
        @if($messages->count() > 0)
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Subject</th>
                        <th>Status</th>
                        <th>Created By</th>
                        <th>Last Updated</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($messages as $message)
                    <tr>
                        <td>#{{ $message->id }}</td>
                        <td>{{ $message->subject }}</td>
                        <td>
                            <span class="badge bg-{{ 
                                $message->status == 'open' ? 'warning' : 
                                ($message->status == 'in_progress' ? 'info' : 'success') 
                            }}">
                                {{ ucfirst($message->status) }}
                            </span>
                        </td>
                        <td>{{ $message->user->name }}</td>
                        <td>{{ $message->updated_at->format('Y-m-d H:i') }}</td>
                        <td>
                            <a href="{{ route('company.support.show', $message) }}" class="btn btn-sm btn-primary">View</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            {{ $messages->links() }}
        </div>
        @else
        <div class="text-center py-5">
            <i class="fas fa-comments fa-3x text-muted mb-3"></i>
            <h4>No Support Tickets</h4>
            <p class="text-muted">Create your first support ticket to get help.</p>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newTicketModal">
                Create First Ticket
            </button>
        </div>
        @endif
    </div>
</div>

<!-- New Ticket Modal -->
<div class="modal fade" id="newTicketModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">New Support Ticket</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('company.support.store') }}">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Subject *</label>
                        <input type="text" class="form-control" name="subject" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Message *</label>
                        <textarea class="form-control" name="message" rows="5" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Submit Ticket</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection