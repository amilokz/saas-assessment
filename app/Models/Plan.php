<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'monthly_price',
        'yearly_price',
        'max_storage_mb',
        'max_users',
        'max_files',
        'features',
        'is_active',
        'is_trial',
        'trial_days',
        'position',
    ];

    protected $casts = [
        'monthly_price' => 'decimal:2',
        'yearly_price' => 'decimal:2',
        'max_storage_mb' => 'integer',
        'max_users' => 'integer',
        'max_files' => 'integer',
        'is_active' => 'boolean',
        'is_trial' => 'boolean',
        'trial_days' => 'integer',
        'position' => 'integer',
        'features' => 'array',
    ];

    // Relationships
    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeTrial($query)
    {
        return $query->where('is_trial', true);
    }

    public function scopePaid($query)
    {
        return $query->where('is_trial', false);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('position')->orderBy('monthly_price');
    }

    // Helper methods
    public function isActive()
    {
        return $this->is_active;
    }

    public function isTrial()
    {
        return $this->is_trial;
    }

    public function getYearlySavingsAttribute()
    {
        if ($this->monthly_price > 0) {
            $yearlyFromMonthly = $this->monthly_price * 12;
            return $yearlyFromMonthly - $this->yearly_price;
        }
        return 0;
    }

    public function getYearlySavingsPercentageAttribute()
    {
        if ($this->monthly_price > 0 && $this->yearly_price > 0) {
            $yearlyFromMonthly = $this->monthly_price * 12;
            return round((($yearlyFromMonthly - $this->yearly_price) / $yearlyFromMonthly) * 100);
        }
        return 0;
    }

    public function getFormattedMonthlyPriceAttribute()
    {
        return '$' . number_format($this->monthly_price, 2);
    }

    public function getFormattedYearlyPriceAttribute()
    {
        return '$' . number_format($this->yearly_price, 2);
    }
}