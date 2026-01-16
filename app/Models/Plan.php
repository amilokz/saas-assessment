<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Plan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'monthly_price',
        'yearly_price',
        'max_users',
        'max_storage_mb',
        'is_active',
        'is_trial',
        'features',
    ];

    protected $casts = [
        'monthly_price' => 'decimal:2',
        'yearly_price' => 'decimal:2',
        'max_users' => 'integer',
        'max_storage_mb' => 'integer',
        'is_active' => 'boolean',
        'is_trial' => 'boolean',
        'features' => 'array',
    ];

    // ✅ Auto-generate slug when creating/updating
    protected static function booted()
    {
        static::creating(function ($plan) {
            if (empty($plan->slug)) {
                $plan->slug = Str::slug($plan->name);
            }
        });

        static::updating(function ($plan) {
            if ($plan->isDirty('name')) {
                $plan->slug = Str::slug($plan->name);
            }
        });
    }

    // Relationships
    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    // Helper methods
    public function isBasic()
    {
        return $this->slug === 'basic';
    }

    public function isPro()
    {
        return $this->slug === 'pro';
    }

    public function isEnterprise()
    {
        return $this->slug === 'enterprise';
    }

    public function isTrialPlan()
    {
        return $this->slug === 'trial';
    }

    public function getPrice($cycle = 'monthly')
    {
        return $cycle === 'yearly' ? $this->yearly_price : $this->monthly_price;
    }

    public function getFormattedPrice($cycle = 'monthly')
    {
        $price = $this->getPrice($cycle);
        return '$' . number_format($price, 2);
    }
}