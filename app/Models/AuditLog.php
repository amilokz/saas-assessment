<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'user_id',
        'action',
        'model_type',
        'model_id',
        'old_data',
        'new_data',
        'ip_address',
        'user_agent',
        'url',
        'description',
        'metadata',
    ];

    protected $casts = [
        'old_data' => 'array',
        'new_data' => 'array',
        'metadata' => 'array',
    ];

    // ✅ ADD THESE ACCESSORS to map 'action' to 'event'
    public function getEventAttribute()
    {
        return $this->action;
    }

    public function setEventAttribute($value)
    {
        $this->attributes['action'] = $value;
    }

    // ✅ Optional: Add helper methods for common queries
    public function scopeByEvent($query, $event)
    {
        return $query->where('action', $event);
    }

    public function scopeByAction($query, $action)
    {
        return $query->where('action', $action);
    }

    // Relationships
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function model()
    {
        return $this->morphTo();
    }

    public function scopeForCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeRecent($query, $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }
}