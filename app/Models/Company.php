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

    // ✅ REMOVED: Global scope
    // protected static function booted()
    // {
    //     static::addGlobalScope('tenant', function ($query) {
    //         $user = auth()->user();
    //         if ($user && !$user->isSuperAdmin()) {
    //             $query->where('id', $user->company_id);
    //         }
    //     });
    // }

    // Relationships
    public function users()
    {
        return $this->hasMany(User::class);
    }

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
        return $this->hasOne(Subscription::class)
                    ->where('status', 'active')
                    ->latest();
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

    public function hasActiveSubscription()
    {
        return $this->subscriptions()->where('status', 'active')->exists();
    }

    public function getTrialDaysLeftAttribute()
    {
        if (!$this->trial_ends_at || !$this->isOnTrial()) {
            return 0;
        }
        
        $days = now()->diffInDays($this->trial_ends_at, false);
        return max(0, $days);
    }
}