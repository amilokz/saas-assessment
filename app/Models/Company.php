<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'admin_name',
        'email',
        'business_type',
        'status',
        'trial_ends_at',
    ];

    protected $casts = [
        'trial_ends_at' => 'datetime',
    ];

    // Add this relationship
    public function users()
    {
        return $this->hasMany(User::class);
    }

    // Add other relationships if needed
    public function files()
    {
        return $this->hasMany(File::class);
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    public function activeSubscription()
    {
        return $this->hasOne(Subscription::class)->where('status', 'active');
    }

    public function invitations()
    {
        return $this->hasMany(Invitation::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function auditLogs()
    {
        return $this->hasMany(AuditLog::class);
    }

    // Helper methods
    public function isOnTrial()
    {
        return $this->status === 'trial_pending_approval' && 
               $this->trial_ends_at && 
               $this->trial_ends_at->isFuture();
    }

    public function isApproved()
    {
        return $this->status === 'approved';
    }

    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function hasActiveSubscription()
    {
        return $this->activeSubscription()->exists();
    }

    public function hasExpiredTrial()
    {
        return $this->status === 'trial_pending_approval' && 
               $this->trial_ends_at && 
               $this->trial_ends_at->isPast();
    }
}