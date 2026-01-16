@extends('layouts.company')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="mb-0">File Management</h4>
        @if($canUpload)
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#uploadModal">
            <i class="fas fa-upload"></i> Upload File
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
        
        <!-- Storage Info -->
        <div class="alert alert-info">
            <h5>Storage Usage</h5>
            <div class="d-flex justify-content-between align-items-center">
                <span>{{ $totalStorageMB }} MB used of {{ $storageLimit }} MB</span>
                <span>{{ $storageUsage }}%</span>
            </div>
            <div class="progress mt-2">
                <div class="progress-bar" role="progressbar" style="width: {{ $storageUsage }}%"></div>
            </div>
        </div>
        
        @if(!$canUpload && auth()->user()->company->isOnTrial())
        <div class="alert alert-warning">
            <i class="fas fa-exclamation-triangle"></i> 
            Trial companies can only upload 2 files. Upgrade to upload more.
        </div>
        @endif
        
        @if($files->count() > 0)
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>File Name</th>
                        <th>Uploaded By</th>
                        <th>Size</th>
                        <th>Type</th>
                        <th>Uploaded</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($files as $file)
                    <tr>
                        <td>{{ $file->original_name }}</td>
                        <td>{{ $file->user->name }}</td>
                        <td>{{ round($file->size / 1024, 2) }} KB</td>
                        <td>{{ ucfirst($file->type) }}</td>
                        <td>{{ $file->created_at->format('Y-m-d') }}</td>
                        <td>
                            <a href="{{ route('company.files.download', $file) }}" class="btn btn-sm btn-primary">Download</a>
                            @if(auth()->user()->isCompanyAdmin() || auth()->user()->isSupportUser())
                            <form method="POST" action="{{ route('company.files.destroy', $file) }}" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" 
                                        onclick="return confirm('Delete this file?')">
                                    Delete
                                </button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            {{ $files->links() }}
        </div>
        @else
        <div class="text-center py-5">
            <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
            <h4>No Files Uploaded</h4>
            <p class="text-muted">Upload your first file to get started.</p>
            @if($canUpload)
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#uploadModal">
                Upload First File
            </button>
            @endif
        </div>
        @endif
    </div>
</div>

<!-- Upload Modal -->
<div class="modal fade" id="uploadModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Upload File</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('company.files.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Select File *</label>
                        <input type="file" class="form-control" name="file" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.txt" required>
                        <small class="text-muted">Max file size: 10MB</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Upload File</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection