<?php

namespace App\Console\Commands;

use App\Models\Company;
use App\Models\User;
use App\Models\Role;
use App\Models\Plan;
use App\Models\File;
use App\Models\Message;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class GenerateTestData extends Command
{
    protected $signature = 'test-data:generate {--count=5}';
    protected $description = 'Generate test data for development';

    public function handle()
    {
        $count = $this->option('count');

        $this->info("Generating {$count} test companies...");

        // Get roles
        $companyAdminRole = Role::where('name', 'company_admin')->first();
        $normalUserRole = Role::where('name', 'normal_user')->first();
        $supportRole = Role::where('name', 'support_user')->first();

        // Get plans
        $basicPlan = Plan::where('slug', 'basic')->first();

        for ($i = 1; $i <= $count; $i++) {
            // Create company
            $company = Company::create([
                'name' => "Test Company {$i}",
                'admin_name' => "Admin {$i}",
                'email' => "company{$i}@test.com",
                'business_type' => 'Technology',
                'status' => 'approved',
            ]);

            // Create admin user
            $admin = User::create([
                'name' => "Admin User {$i}",
                'email' => "admin{$i}@test.com",
                'password' => Hash::make('password123'),
                'company_id' => $company->id,
                'role_id' => $companyAdminRole->id,
                'email_verified_at' => now(),
            ]);

            // Create 2 normal users
            for ($j = 1; $j <= 2; $j++) {
                User::create([
                    'name' => "User {$i}-{$j}",
                    'email' => "user{$i}-{$j}@test.com",
                    'password' => Hash::make('password123'),
                    'company_id' => $company->id,
                    'role_id' => $normalUserRole->id,
                    'email_verified_at' => now(),
                ]);
            }

            // Create support user
            User::create([
                'name' => "Support {$i}",
                'email' => "support{$i}@test.com",
                'password' => Hash::make('password123'),
                'company_id' => $company->id,
                'role_id' => $supportRole->id,
                'email_verified_at' => now(),
            ]);

            // Create files
            for ($k = 1; $k <= 3; $k++) {
                File::create([
                    'company_id' => $company->id,
                    'user_id' => $admin->id,
                    'name' => "file-{$i}-{$k}.txt",
                    'original_name' => "Document {$i}-{$k}.txt",
                    'path' => "companies/{$company->id}/files/file-{$i}-{$k}.txt",
                    'mime_type' => 'text/plain',
                    'size' => 1024 * $k,
                    'type' => 'document',
                    'is_public' => $k % 2 == 0,
                ]);
            }

            // Create support messages
            for ($l = 1; $l <= 2; $l++) {
                Message::create([
                    'company_id' => $company->id,
                    'user_id' => $admin->id,
                    'message' => "This is test support message {$l} for company {$i}.",
                    'subject' => "Support Request {$l}",
                    'type' => 'support',
                    'status' => $l % 2 == 0 ? 'open' : 'closed',
                ]);
            }

            $this->line("Created company: {$company->name} with users and data");
        }

        $this->info("Test data generation completed!");
        
        return Command::SUCCESS;
    }
}