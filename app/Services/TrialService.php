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
}