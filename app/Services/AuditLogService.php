<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\Company;
use App\Models\Subscription;
use App\Models\File;
use App\Models\Invitation;

class AuditLogService
{
    public static function log($event, $model = null, $description = null): void
    {
        $oldValues = [];
        $newValues = [];
        
        if ($model && method_exists($model, 'getOriginal')) {
            $oldValues = $model->getOriginal();
            $newValues = $model->getAttributes();
        }

        AuditLog::create([
            'company_id' => auth()->check() ? auth()->user()->company_id : null,
            'user_id' => auth()->id(),
            'event' => $event,  // Changed from 'action' to 'event'
            'model_type' => $model ? get_class($model) : null,
            'model_id' => $model ? $model->id : null,
            'old_values' => $oldValues,  // Changed from 'old_data' to 'old_values'
            'new_values' => $newValues,  // Changed from 'new_data' to 'new_values'
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'url' => request()->fullUrl(),
            'description' => $description,
        ]);
    }

    public static function logCompanyRegistration(Company $company): void
    {
        self::log('company_registered', $company, 'Company registered for trial');
    }

    public static function logCompanyApproval(Company $company, bool $approved): void
    {
        $action = $approved ? 'approved' : 'rejected';
        self::log('company_' . $action, $company, "Company {$action} by super admin");
    }

    public static function logSubscriptionCreated(Subscription $subscription): void
    {
        self::log('subscription_created', $subscription, 'New subscription created');
    }

    public static function logFileUpload(File $file): void
    {
        self::log('file_uploaded', $file, 'File uploaded');
    }

    public static function logFileDeleted(File $file): void
    {
        self::log('file_deleted', $file, 'File deleted');
    }

    public static function logUserInvitation(Invitation $invitation): void
    {
        self::log('user_invited', $invitation, 'User invitation sent');
    }
}