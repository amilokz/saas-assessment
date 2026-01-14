<?php

namespace App\Console\Commands;

use App\Models\Invitation;
use Illuminate\Console\Command;

class CleanupExpiredInvitations extends Command
{
    protected $signature = 'invitations:cleanup';
    protected $description = 'Clean up expired invitations';

    public function handle()
    {
        $expired = Invitation::where('status', 'pending')
            ->where('expires_at', '<=', now())
            ->update(['status' => 'expired']);

        $this->info("Marked {$expired} invitations as expired.");
        
        return Command::SUCCESS;
    }
}