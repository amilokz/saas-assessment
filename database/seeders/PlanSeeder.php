<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Plan;
use Illuminate\Support\Facades\DB;

class PlanSeeder extends Seeder
{
    public function run()
    {
        // Clear existing plans
        DB::table('plans')->truncate();

        $plans = [
            [
                'name' => 'Basic',
                'slug' => 'basic',
                'description' => 'Basic plan with limited features',
                'monthly_price' => 19.99,
                'yearly_price' => 199.99, // ~2 months free
                'max_users' => 10,
                'max_files' => 100,
                'max_storage_mb' => 1024, // 1GB
                'features' => json_encode([
                    'Basic Support',
                    'File Storage (1GB)',
                    'Up to 10 users',
                    'Audit Logs (30 days)'
                ]),
                'is_active' => true,
            ],
            [
                'name' => 'Pro',
                'slug' => 'pro',
                'description' => 'Pro plan with advanced features',
                'monthly_price' => 49.99,
                'yearly_price' => 499.99, // ~2 months free
                'max_users' => 50,
                'max_files' => 500,
                'max_storage_mb' => 5120, // 5GB
                'features' => json_encode([
                    'Priority Support',
                    'File Storage (5GB)',
                    'Up to 50 users',
                    'Audit Logs (90 days)',
                    'Advanced Analytics'
                ]),
                'is_active' => true,
            ],
            [
                'name' => 'Enterprise',
                'slug' => 'enterprise',
                'description' => 'Enterprise plan with unlimited features',
                'monthly_price' => 99.99,
                'yearly_price' => 999.99, // ~2 months free
                'max_users' => null, // Unlimited
                'max_files' => null, // Unlimited
                'max_storage_mb' => null, // Unlimited
                'features' => json_encode([
                    '24/7 Support',
                    'Unlimited Storage',
                    'Unlimited Users',
                    'Unlimited Audit Logs',
                    'Advanced Analytics',
                    'Custom Integrations',
                    'Dedicated Account Manager'
                ]),
                'is_active' => true,
            ],
        ];

        foreach ($plans as $plan) {
            Plan::create($plan);
        }

        $this->command->info('✅ Plans seeded successfully!');
    }
}