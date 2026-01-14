<?php

namespace App\Console\Commands;

use App\Models\Company;
use App\Services\AuditLogService;
use Illuminate\Console\Command;

class CleanupExpiredTrials extends Command
{
    protected $signature = 'trials:cleanup';
    protected $description = 'Clean up expired trial companies';

    public function handle()
    {
        $expiredTrials = Company::where('status', 'trial_pending_approval')
            ->where('trial_ends_at', '<', now())
            ->get();

        $count = 0;

        foreach ($expiredTrials as $company) {
            $company->update(['status' => 'suspended']);
            
            // Log the suspension
            AuditLogService::log('trial_expired', $company, 'Trial expired and account suspended');
            
            $count++;
            $this->info("Suspended trial for company: {$company->name}");
        }

        $this->info("Cleaned up {$count} expired trials.");
        
        return Command::SUCCESS;
    }
}