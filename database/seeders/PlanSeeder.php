<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    public function run(): void
    {
        $plans = [
            [
                'name' => 'Basic',
                'slug' => 'basic',
                'description' => 'Basic plan for small businesses',
                'monthly_price' => 29.99,
                'yearly_price' => 299.99,
                'max_users' => 5,
                'max_storage_mb' => 1024,
                'is_active' => true,
                'is_trial' => false,
                'features' => json_encode(['Basic Support', '5 Users', '1GB Storage'])
            ],
            [
                'name' => 'Pro',
                'slug' => 'pro',
                'description' => 'Pro plan for growing businesses',
                'monthly_price' => 59.99,
                'yearly_price' => 599.99,
                'max_users' => 20,
                'max_storage_mb' => 5120,
                'is_active' => true,
                'is_trial' => false,
                'features' => json_encode(['Priority Support', '20 Users', '5GB Storage', 'Advanced Analytics'])
            ],
            [
                'name' => 'Enterprise',
                'slug' => 'enterprise',
                'description' => 'Enterprise plan for large organizations',
                'monthly_price' => 199.99,
                'yearly_price' => 1999.99,
                'max_users' => 100,
                'max_storage_mb' => 10240,
                'is_active' => true,
                'is_trial' => false,
                'features' => json_encode(['24/7 Support', '100 Users', '10GB Storage', 'Custom Features', 'Dedicated Account Manager'])
            ],
        ];

        foreach ($plans as $plan) {
            Plan::firstOrCreate(
                ['slug' => $plan['slug']],
                $plan
            );
        }

        $this->command->info('Plans created successfully!');
    }
}