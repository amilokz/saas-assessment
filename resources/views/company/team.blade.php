@extends('layouts.company')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="mb-0">Team Management</h4>
        @if($canInvite)
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#inviteModal">
            <i class="fas fa-user-plus"></i> Invite User
        </button>
        @endif
    </div>
    <div class="card-body">
        @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        
        @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        
        @if($company->isOnTrial())
        <div class="alert alert-warning">
            <i class="fas fa-exclamation-triangle"></i> 
            Trial companies can only have 1 user. Upgrade to invite more team members.
        </div>
        @endif
        
        <h5>Team Members ({{ $users->count() }})</h5>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Joined</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                    <tr>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>
                            @if(auth()->id() !== $user->id)
                            <form method="POST" action="{{ route('company.team.user.role', $user) }}" class="d-inline">
                                @csrf
                                <select name="role_id" class="form-select form-select-sm" onchange="this.form.submit()">
                                    @foreach($roles as $role)
                                    <option value="{{ $role->id }}" {{ $user->role_id == $role->id ? 'selected' : '' }}>
                                        {{ $role->display_name }}
                                    </option>
                                    @endforeach
                                </select>
                            </form>
                            @else
                            <span class="badge bg-primary">{{ $user->role->display_name ?? 'No Role' }}</span>
                            @endif
                        </td>
                        <td>{{ $user->created_at ? $user->created_at->format('Y-m-d') : 'N/A' }}</td>
                        <td>
                            @if(auth()->id() !== $user->id)
                            <form method="POST" action="{{ route('company.team.user.remove', $user) }}" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" 
                                        onclick="return confirm('Remove this user?')">
                                    Remove
                                </button>
                            </form>
                            @else
                            <span class="text-muted">You</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        @if($invitations->count() > 0)
        <h5 class="mt-4">Pending Invitations</h5>
        <div class="table-responsive">
            <table class="table table-sm">
                <thead>
                    <tr>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Invited By</th>
                        <th>Expires</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($invitations as $invitation)
                    <tr>
                        <td>{{ $invitation->email }}</td>
                        <td>{{ $invitation->role->display_name ?? 'No Role' }}</td>
                        <td>{{ $invitation->inviter->name ?? 'System' }}</td>
                        <td>{{ $invitation->expires_at ? $invitation->expires_at->format('Y-m-d') : 'Never' }}</td>
                        <td>
                            <form method="POST" action="{{ route('company.team.invitation.resend', $invitation) }}" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-warning">Resend</button>
                            </form>
                            <form method="POST" action="{{ route('company.team.invitation.revoke', $invitation) }}" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-danger">Revoke</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
</div>

<!-- Invite Modal -->
<div class="modal fade" id="inviteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Invite Team Member</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('company.team.invite') }}">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Email Address *</label>
                        <input type="email" class="form-control" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Role *</label>
                        <select class="form-select" name="role_id" required>
                            @foreach($roles as $role)
                            <option value="{{ $role->id }}">{{ $role->display_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Custom Message (Optional)</label>
                        <textarea class="form-control" name="message" rows="2" 
                                  placeholder="Add a personal message to the invitation..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Send Invitation</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- JavaScript for better UX -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-focus email input when modal opens
    const inviteModal = document.getElementById('inviteModal');
    if (inviteModal) {
        inviteModal.addEventListener('shown.bs.modal', function() {
            document.querySelector('#inviteModal input[name="email"]').focus();
        });
    }
    
    // Prevent form submission if trial limit reached
    const inviteForm = document.querySelector('#inviteModal form');
    if (inviteForm) {
        inviteForm.addEventListener('submit', function(e) {
            const trialAlert = document.querySelector('.alert-warning');
            if (trialAlert && trialAlert.textContent.includes('Trial companies can only have 1 user')) {
                if (!confirm('Trial companies are limited to 1 user. Are you sure you want to invite?')) {
                    e.preventDefault();
                }
            }
        });
    }
});
</script>
@endsection