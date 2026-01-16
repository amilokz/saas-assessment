<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\Company;
use App\Models\Subscription;
use App\Models\File;
use App\Models\Invitation;
use Illuminate\Support\Facades\Schema;

class AuditLogService
{
    // ✅ FIXED: Support both 'action' and 'event' parameters
    public function log(array $data)
    {
        // Use 'event' if provided, otherwise 'action'
        $action = $data['event'] ?? $data['action'] ?? 'unknown';
        
        $logData = [
            'company_id' => $data['company_id'] ?? null,
            'user_id' => $data['user_id'] ?? null,
            'action' => $action,
            'model_type' => $data['model_type'] ?? null,
            'model_id' => $data['model_id'] ?? null,
            'old_data' => $data['old_data'] ?? $data['old_values'] ?? null,
            'new_data' => $data['new_data'] ?? $data['new_values'] ?? null,
            'ip_address' => $data['ip_address'] ?? request()->ip(),
            'user_agent' => $data['user_agent'] ?? request()->userAgent(),
            'url' => $data['url'] ?? request()->fullUrl(),
            'description' => $data['description'] ?? null,
            'metadata' => $data['metadata'] ?? null,
        ];

        return AuditLog::create($logData);
    }

    // ✅ FIXED: Support both 'action' and 'event' in static method
    public static function logStatic($actionOrEvent, $model = null, $description = null, $metadata = null): void
    {
        try {
            $oldData = [];
            $newData = [];
            
            if ($model && method_exists($model, 'getOriginal')) {
                $oldData = $model->getOriginal();
                $newData = $model->getAttributes();
            }

            AuditLog::create([
                'company_id' => auth()->check() && auth()->user()->company ? auth()->user()->company_id : null,
                'user_id' => auth()->id(),
                'action' => $actionOrEvent, // Store as 'action' in DB
                'model_type' => $model ? get_class($model) : null,
                'model_id' => $model ? $model->id : null,
                'old_data' => $oldData ?: null,
                'new_data' => $newData ?: null,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'url' => request()->fullUrl(),
                'description' => $description,
                'metadata' => $metadata,
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Audit log failed', [
                'error' => $e->getMessage(),
                'action' => $actionOrEvent,
                'model' => $model ? get_class($model) : null,
            ]);
        }
    }

    // ✅ FIXED: Add missing methods for suspension
    public static function logCompanySuspension(Company $company, bool $suspended): void
    {
        $action = $suspended ? 'company_suspended' : 'company_activated';
        self::logStatic($action, $company, "Company was " . ($suspended ? 'suspended' : 'activated'));
    }

    public static function logCompanyRegistration(Company $company): void
    {
        self::logStatic('company_registered', $company, 'Company registered for trial');
    }

    public static function logCompanyApproval(Company $company, bool $approved): void
    {
        $action = $approved ? 'company_approved' : 'company_rejected';
        self::logStatic($action, $company, "Company {$action} by super admin");
    }

    public static function logSubscriptionCreated(Subscription $subscription): void
    {
        self::logStatic('subscription_created', $subscription, 'New subscription created');
    }

    public static function logFileUpload(File $file): void
    {
        self::logStatic('file_uploaded', $file, 'File uploaded');
    }

    public static function logFileDeleted(File $file): void
    {
        self::logStatic('file_deleted', $file, 'File deleted');
    }

    public static function logUserInvitation(Invitation $invitation): void
    {
        self::logStatic('user_invited', $invitation, 'User invitation sent');
    }
}