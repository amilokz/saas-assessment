<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'plan_id',
        'stripe_subscription_id',
        'stripe_customer_id',
        'billing_cycle',
        'amount',
        'status',
        'trial_ends_at',
        'ends_at',
        'cancelled_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'trial_ends_at' => 'datetime',
        'ends_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    // Relationships
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    public function scopePastDue($query)
    {
        return $query->where('status', 'past_due');
    }

    // Helper methods
    public function isActive()
    {
        return $this->status === 'active';
    }

    public function isCancelled()
    {
        return $this->status === 'cancelled';
    }

    public function isPastDue()
    {
        return $this->status === 'past_due';
    }

    public function isOnTrial()
    {
        return $this->trial_ends_at && $this->trial_ends_at->isFuture();
    }

    public function hasEnded()
    {
        return $this->ends_at && $this->ends_at->isPast();
    }

    public function getMonthlyAmountAttribute()
    {
        if ($this->billing_cycle === 'yearly') {
            return $this->amount / 12;
        }
        return $this->amount;
    }

    public function getYearlyAmountAttribute()
    {
        if ($this->billing_cycle === 'monthly') {
            return $this->amount * 12;
        }
        return $this->amount;
    }
}