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
        'event',           // Changed from 'action' to 'event'
        'model_type',
        'model_id',
        'old_values',      // Changed from 'old_data' to 'old_values'
        'new_values',      // Changed from 'new_data' to 'new_values'
        'ip_address',
        'user_agent',
        'url',
        'description',
    ];

    protected $casts = [
        'old_values' => 'array',  // Changed from 'old_data' to 'old_values'
        'new_values' => 'array',  // Changed from 'new_data' to 'new_values'
    ];

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

    // Scopes
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

    public function scopeByEvent($query, $event)
    {
        return $query->where('event', $event);
    }
}