<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        // Find super admin role
        $superAdminRole = Role::where('name', 'super_admin')->first();
        
        if (!$superAdminRole) {
            $this->command->error('Super admin role not found. Run RoleSeeder first.');
            return;
        }

        // Check if super admin already exists
        $existingSuperAdmin = User::where('email', 'admin@saas.test')->first();
        
        if (!$existingSuperAdmin) {
            // Create super admin user
            User::create([
                'name' => 'Super Admin',
                'email' => 'admin@saas.test',
                'password' => Hash::make('password123'),
                'role_id' => $superAdminRole->id,
                'company_id' => null,
                'email_verified_at' => now(),
                'is_active' => true,
            ]);
            
            $this->command->info('Super admin user created successfully!');
            $this->command->info('Email: admin@saas.test');
            $this->command->info('Password: password123');
        } else {
            $this->command->info('Super admin user already exists.');
        }
    }
}