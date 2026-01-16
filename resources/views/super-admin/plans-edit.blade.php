@extends('layouts.super-admin')

@section('title', 'Edit Plan')

@section('content')
<div class="card">
    <div class="card-header">
        <h4 class="mb-0">Edit Plan: {{ $plan->name }}</h4>
    </div>
    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
        
        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        
        <form method="POST" action="{{ route('super-admin.plans.update', $plan) }}">
            @csrf
            @method('PUT')
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="name" class="form-label">Plan Name *</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                           id="name" name="name" value="{{ old('name', $plan->name) }}" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label for="slug" class="form-label">Slug *</label>
                    <input type="text" class="form-control @error('slug') is-invalid @enderror" 
                           id="slug" name="slug" value="{{ old('slug', $plan->slug) }}" required>
                    @error('slug')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="text-muted">URL-friendly identifier (lowercase, hyphens)</small>
                </div>
            </div>
            
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control @error('description') is-invalid @enderror" 
                          id="description" name="description" rows="2">{{ old('description', $plan->description) }}</textarea>
                @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="monthly_price" class="form-label">Monthly Price ($) *</label>
                    <input type="number" step="0.01" min="0" 
                           class="form-control @error('monthly_price') is-invalid @enderror" 
                           id="monthly_price" name="monthly_price" 
                           value="{{ old('monthly_price', $plan->monthly_price) }}" required>
                    @error('monthly_price')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label for="yearly_price" class="form-label">Yearly Price ($) *</label>
                    <input type="number" step="0.01" min="0" 
                           class="form-control @error('yearly_price') is-invalid @enderror" 
                           id="yearly_price" name="yearly_price" 
                           value="{{ old('yearly_price', $plan->yearly_price) }}" required>
                    @error('yearly_price')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="max_users" class="form-label">Max Users</label>
                    <input type="number" min="0" 
                           class="form-control @error('max_users') is-invalid @enderror" 
                           id="max_users" name="max_users" 
                           value="{{ old('max_users', $plan->max_users) }}">
                    @error('max_users')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="text-muted">0 = unlimited</small>
                </div>
                <div class="col-md-4">
                    <label for="max_files" class="form-label">Max Files</label>
                    <input type="number" min="0" 
                           class="form-control @error('max_files') is-invalid @enderror" 
                           id="max_files" name="max_files" 
                           value="{{ old('max_files', $plan->max_files) }}">
                    @error('max_files')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="text-muted">0 = unlimited</small>
                </div>
                <div class="col-md-4">
                    <label for="max_storage_mb" class="form-label">Max Storage (MB)</label>
                    <input type="number" min="0" 
                           class="form-control @error('max_storage_mb') is-invalid @enderror" 
                           id="max_storage_mb" name="max_storage_mb" 
                           value="{{ old('max_storage_mb', $plan->max_storage_mb) }}">
                    @error('max_storage_mb')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror>
                    <small class="text-muted">1024 MB = 1 GB</small>
                </div>
            </div>
            
            <div class="mb-3">
                <label for="features" class="form-label">Features</label>
                <textarea class="form-control @error('features') is-invalid @enderror" 
                          id="features" name="features" rows="3"
                          placeholder="Enter features separated by commas or new lines">{{ old('features', is_array(json_decode($plan->features, true)) ? implode(', ', json_decode($plan->features, true)) : $plan->features) }}</textarea>
                @error('features')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <small class="text-muted">Separate features with commas or new lines</small>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-4">
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" 
                               id="is_active" name="is_active" value="1"
                               {{ old('is_active', $plan->is_active) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active">Active Plan</label>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" 
                               id="is_trial" name="is_trial" value="1"
                               {{ old('is_trial', $plan->is_trial) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_trial">Trial Plan</label>
                    </div>
                </div>
                <div class="col-md-4">
                    <label for="trial_days" class="form-label">Trial Days</label>
                    <input type="number" min="0" 
                           class="form-control @error('trial_days') is-invalid @enderror" 
                           id="trial_days" name="trial_days" 
                           value="{{ old('trial_days', $plan->trial_days) }}">
                    @error('trial_days')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            @if(\Schema::hasColumn('plans', 'sort_order'))
            <div class="mb-3">
                <label for="sort_order" class="form-label">Sort Order</label>
                <input type="number" min="0" 
                       class="form-control @error('sort_order') is-invalid @enderror" 
                       id="sort_order" name="sort_order" 
                       value="{{ old('sort_order', $plan->sort_order ?? 0) }}">
                @error('sort_order')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror>
                <small class="text-muted">Lower numbers appear first</small>
            </div>
            @endif
            
            <div class="mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Update Plan
                </button>
                <a href="{{ route('super-admin.plans.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancel
                </a>
                <button type="button" class="btn btn-danger" 
                        onclick="if(confirm('Are you sure you want to delete this plan?')) { 
                            document.getElementById('delete-form').submit(); 
                        }">
                    <i class="fas fa-trash"></i> Delete Plan
                </button>
            </div>
        </form>
        
        <!-- Delete Form -->
        <form id="delete-form" 
              action="{{ route('super-admin.plans.destroy', $plan) }}" 
              method="POST" class="d-none">
            @csrf
            @method('DELETE')
        </form>
    </div>
</div>

<!-- JavaScript to auto-generate slug from name -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const nameInput = document.getElementById('name');
    const slugInput = document.getElementById('slug');
    
    nameInput.addEventListener('input', function() {
        if (!slugInput.dataset.manualEdit) {
            const slug = nameInput.value
                .toLowerCase()
                .replace(/[^a-z0-9\s-]/g, '')
                .replace(/\s+/g, '-')
                .replace(/-+/g, '-')
                .trim();
            slugInput.value = slug;
        }
    });
    
    slugInput.addEventListener('input', function() {
        if (slugInput.value) {
            slugInput.dataset.manualEdit = true;
        }
    });
    
    // Format features textarea for better editing
    const featuresTextarea = document.getElementById('features');
    if (featuresTextarea) {
        featuresTextarea.addEventListener('blur', function() {
            // Convert new lines to commas and clean up
            let features = this.value
                .split(/[\n,]/)
                .map(feature => feature.trim())
                .filter(feature => feature.length > 0)
                .join(', ');
            this.value = features;
        });
    }
});
</script>
@endsection