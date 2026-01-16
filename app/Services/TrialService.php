<?php

namespace App\Services;

use App\Models\Company;
use Carbon\Carbon;

class TrialService
{
    public function startTrial(Company $company): void
    {
        $company->update([
            'status' => 'trial_pending_approval',
            'trial_ends_at' => Carbon::now()->addDays(7),
        ]);
    }

    public function applyTrialLimitations(Company $company): void
    {
        // ✅ SIMPLE: Just start the trial - limitations are handled in Company model
        $this->startTrial($company);
    }

    public function checkTrialExpiry(Company $company): bool
    {
        if ($company->status !== 'trial_pending_approval') {
            return false;
        }

        if (!$company->trial_ends_at) {
            return false;
        }

        if ($company->trial_ends_at->isPast()) {
            $company->update(['status' => 'suspended']);
            return true;
        }

        return false;
    }

    public function getRemainingTrialDays(Company $company): ?int
    {
        if ($company->status !== 'trial_pending_approval' || !$company->trial_ends_at) {
            return null;
        }

        return max(0, Carbon::now()->diffInDays($company->trial_ends_at, false));
    }

    public function getTrialLimitations(): array
    {
        return [
            'max_users' => 1,
            'max_files' => 2,
            'max_storage_mb' => 100,
            'can_subscribe' => false,
            'can_invite' => true,
            'max_invitations' => 1,
        ];
    }

    public function canUploadFile(Company $company): bool
    {
        return $company->canUploadMoreFiles();
    }

    public function canInviteUser(Company $company): bool
    {
        return $company->canInviteMoreUsers();
    }

    public function checkIfTrialExceeded(Company $company, string $type): bool
    {
        switch ($type) {
            case 'files':
                return !$company->canUploadMoreFiles();
            case 'users':
                return !$company->canInviteMoreUsers();
            case 'storage':
                return false; // Not implemented yet
            default:
                return false;
        }
    }

    public function getTrialExceededMessage(Company $company, string $type): string
    {
        $limitations = $this->getTrialLimitations();
        
        switch ($type) {
            case 'files':
                return "Trial companies can only upload {$limitations['max_files']} files";
            case 'users':
                return "Trial companies can only have {$limitations['max_users']} user";
            case 'storage':
                return "Trial companies are limited to {$limitations['max_storage_mb']} MB storage";
            default:
                return "Trial limitation exceeded";
        }
    }

    public function removeTrialLimitations(Company $company): void
    {
        // Remove trial status when company is approved
        $company->update([
            'status' => 'active',
            'trial_ends_at' => null,
        ]);
    }

    public function isTrialActive(Company $company): bool
    {
        return $company->isOnTrial();
    }
}