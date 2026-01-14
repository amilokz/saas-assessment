<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'company_id',
        'role_id',
        'email_verified_at',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    // Helper methods
    public function isSuperAdmin()
    {
        return $this->role && $this->role->name === 'super_admin';
    }

    public function isCompanyAdmin()
    {
        return $this->role && $this->role->name === 'company_admin';
    }

    public function isSupportUser()
    {
        return $this->role && $this->role->name === 'support_user';
    }

    public function isNormalUser()
    {
        return $this->role && $this->role->name === 'normal_user';
    }

    // ADD THESE PERMISSION METHODS:
    public function canInviteUsers()
    {
        return $this->isCompanyAdmin();
    }

    public function canUploadFiles()
    {
        return in_array($this->role->name ?? '', ['company_admin', 'support_user']);
    }

    public function canDeleteFiles()
    {
        return $this->isCompanyAdmin();
    }

    public function canManageSubscription()
    {
        return $this->isCompanyAdmin();
    }

    public function canViewAuditLogs()
    {
        return in_array($this->role->name ?? '', ['company_admin', 'support_user']);
    }

    public function canReplyToSupport()
    {
        return in_array($this->role->name ?? '', ['company_admin', 'support_user']);
    }
}