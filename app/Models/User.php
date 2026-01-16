<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Hash;

class User extends Authenticatable
{
    use Notifiable, HasFactory;

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
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $appends = ['role_name', 'joined_date'];

    /**
     * The "booted" method of the model.
     */
    protected static function booted()
    {
        // Set default timestamps if not provided
        static::creating(function ($user) {
            if (empty($user->created_at)) {
                $user->created_at = now();
            }
            if (empty($user->updated_at)) {
                $user->updated_at = now();
            }
            
            // Set default role if not provided
            if (empty($user->role_id)) {
                $defaultRole = Role::where('name', 'normal_user')->first();
                if ($defaultRole) {
                    $user->role_id = $defaultRole->id;
                }
            }
            
            // Set default active status
            if (is_null($user->is_active)) {
                $user->is_active = true;
            }
        });

        // Always hash password when setting
        static::saving(function ($user) {
            if ($user->isDirty('password')) {
                $user->password = Hash::make($user->password);
            }
        });
    }

    // Relationships
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function files()
    {
        return $this->hasMany(File::class);
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function sentInvitations()
    {
        return $this->hasMany(Invitation::class, 'invited_by');
    }

    public function invitations()
    {
        return $this->hasMany(Invitation::class, 'email', 'email');
    }

    public function subscriptions()
    {
        return $this->hasManyThrough(Subscription::class, Company::class, 'id', 'company_id', 'company_id');
    }

    public function auditLogs()
    {
        return $this->hasMany(AuditLog::class);
    }

    // Accessors
    public function getRoleNameAttribute()
    {
        return $this->role ? $this->role->display_name : 'No Role';
    }

    public function getJoinedDateAttribute()
    {
        return $this->created_at ? $this->created_at->format('Y-m-d') : 'N/A';
    }

    public function getIsOnTrialAttribute()
    {
        return $this->company && $this->company->isOnTrial();
    }

    // Role check methods with caching
    public function isSuperAdmin()
    {
        if (!$this->relationLoaded('role')) {
            $this->load('role');
        }
        return $this->role && $this->role->name === 'super_admin';
    }

    public function isCompanyAdmin()
    {
        if (!$this->relationLoaded('role')) {
            $this->load('role');
        }
        return $this->role && $this->role->name === 'company_admin';
    }

    public function isSupportUser()
    {
        if (!$this->relationLoaded('role')) {
            $this->load('role');
        }
        return $this->role && $this->role->name === 'support_user';
    }

    public function isNormalUser()
    {
        if (!$this->relationLoaded('role')) {
            $this->load('role');
        }
        return $this->role && $this->role->name === 'normal_user';
    }

    // Permission methods
    public function canViewAuditLogs()
    {
        return $this->isCompanyAdmin() || $this->isSupportUser();
    }

    public function canUploadFiles()
    {
        return $this->isCompanyAdmin() || $this->isSupportUser();
    }

    public function canDeleteFiles()
    {
        return $this->isCompanyAdmin() || $this->isSupportUser();
    }

    public function canInviteUsers()
    {
        return $this->isCompanyAdmin();
    }

    public function canManageSubscription()
    {
        return $this->isCompanyAdmin();
    }

    public function canReplyToSupport()
    {
        return $this->isCompanyAdmin() || $this->isSupportUser();
    }

    public function canManageTeam()
    {
        return $this->isCompanyAdmin();
    }

    // Scope queries
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    public function scopeByRole($query, $roleName)
    {
        return $query->whereHas('role', function ($q) use ($roleName) {
            $q->where('name', $roleName);
        });
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('email', 'like', "%{$search}%");
        });
    }

    // Helper methods
    public function activate()
    {
        $this->update(['is_active' => true]);
        return $this;
    }

    public function deactivate()
    {
        $this->update(['is_active' => false]);
        return $this;
    }

    public function changeRole($roleId)
    {
        $this->update(['role_id' => $roleId]);
        $this->load('role');
        return $this;
    }

    public function getAvatarUrl($size = 40)
    {
        // You can implement Gravatar or custom avatar here
        $hash = md5(strtolower(trim($this->email)));
        return "https://www.gravatar.com/avatar/{$hash}?s={$size}&d=identicon";
    }

    public function getInitials()
    {
        $words = explode(' ', $this->name);
        $initials = '';
        
        foreach ($words as $word) {
            if (!empty($word)) {
                $initials .= strtoupper($word[0]);
            }
        }
        
        return substr($initials, 0, 2);
    }

    /**
     * Check if user has permission
     */
    public function hasPermission($permission)
    {
        $permissions = [
            'view_dashboard' => true,
            'view_profile' => true,
            'edit_profile' => true,
            
            // Company Admin permissions
            'manage_subscription' => $this->isCompanyAdmin(),
            'manage_team' => $this->isCompanyAdmin(),
            'invite_users' => $this->isCompanyAdmin(),
            
            // Support User permissions
            'reply_support' => $this->isCompanyAdmin() || $this->isSupportUser(),
            'upload_files' => $this->isCompanyAdmin() || $this->isSupportUser(),
            'delete_files' => $this->isCompanyAdmin() || $this->isSupportUser(),
            'view_audit_logs' => $this->isCompanyAdmin() || $this->isSupportUser(),
            
            // Super Admin permissions (only for super admin)
            'manage_companies' => $this->isSuperAdmin(),
            'manage_plans' => $this->isSuperAdmin(),
            'view_all_audit_logs' => $this->isSuperAdmin(),
        ];

        return $permissions[$permission] ?? false;
    }
}