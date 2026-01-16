@extends('layouts.company')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <div>
            <h4 class="mb-0">Support Ticket: {{ $message->subject }}</h4>
            <small class="text-muted">Ticket #{{ $message->id }}</small>
        </div>
        <div>
            <span class="badge bg-{{ 
                $message->status == 'open' ? 'warning' : 
                ($message->status == 'in_progress' ? 'info' : 'success') 
            }}">
                {{ ucfirst($message->status) }}
            </span>
        </div>
    </div>
    <div class="card-body">
        @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        
        <!-- Original Message -->
        <div class="card mb-4">
            <div class="card-header">
                <strong>{{ $message->user->name }}</strong>
                <small class="text-muted float-end">{{ $message->created_at->format('M d, Y H:i') }}</small>
            </div>
            <div class="card-body">
                <p class="mb-0">{{ $message->message }}</p>
            </div>
        </div>
        
        <!-- Replies -->
        @if($message->replies->count() > 0)
        <h5>Replies ({{ $message->replies->count() }})</h5>
        @foreach($message->replies as $reply)
        <div class="card mb-3">
            <div class="card-header">
                <strong>{{ $reply->user->name }}</strong>
                <small class="text-muted float-end">{{ $reply->created_at->format('M d, Y H:i') }}</small>
            </div>
            <div class="card-body">
                <p class="mb-0">{{ $reply->message }}</p>
            </div>
        </div>
        @endforeach
        @endif
        
        <!-- Reply Form -->
        @if(auth()->user()->isCompanyAdmin() || auth()->user()->isSupportUser())
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="mb-0">Reply to Ticket</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('support.reply', $message) }}">
                    @csrf
                    <div class="mb-3">
                        <textarea class="form-control" name="message" rows="4" required placeholder="Type your reply here..."></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Send Reply</button>
                </form>
            </div>
        </div>
        @endif
        
        <!-- Ticket Actions -->
        <div class="mt-4">
            @if($message->status === 'open' && (auth()->user()->isCompanyAdmin() || auth()->user()->isSupportUser()))
            <form method="POST" action="{{ route('support.close', $message) }}" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-warning">Close Ticket</button>
            </form>
            @endif
            
            @if($message->status === 'closed')
            <form method="POST" action="{{ route('support.reopen', $message) }}" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-info">Reopen Ticket</button>
            </form>
            @endif
            
            <a href="{{ route('company.support') }}" class="btn btn-secondary">Back to Tickets</a>
        </div>
    </div>
</div>
@endsection