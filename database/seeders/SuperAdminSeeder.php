<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use App\Models\Company;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    public function run()
    {
        // First, create a super admin company if it doesn't exist
        $superAdminCompany = Company::firstOrCreate(
            ['email' => 'admin@platform.com'],
            [
                'name' => 'Platform Administration',
                'admin_name' => 'Super Admin',
                'business_type' => 'Platform',
                'status' => 'approved',
                'trial_ends_at' => null,
            ]
        );

        // Get the super admin role
        $superAdminRole = Role::where('name', 'super_admin')->first();

        if (!$superAdminRole) {
            $this->command->error('❌ Super Admin role not found! Run RoleSeeder first.');
            return;
        }

        // Create or update the super admin user
        $superAdmin = User::updateOrCreate(
            ['email' => 'superadmin@example.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password123'),
                'company_id' => $superAdminCompany->id,
                'role_id' => $superAdminRole->id,
                'email_verified_at' => now(),
                // Removed 'is_active' since column doesn't exist
            ]
        );

        $this->command->info('✅ Super Admin created successfully!');
        $this->command->info('📧 Email: superadmin@example.com');
        $this->command->info('🔑 Password: password123');
        $this->command->warn('⚠️  Please change the password after first login!');
    }
}