<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            [
                'name' => 'super_admin',
                'display_name' => 'Super Administrator',
                'description' => 'Full system access'
            ],
            [
                'name' => 'company_admin',
                'display_name' => 'Company Administrator',
                'description' => 'Full company management access'
            ],
            [
                'name' => 'support_user',
                'display_name' => 'Support User',
                'description' => 'Can reply to support messages and upload files'
            ],
            [
                'name' => 'normal_user',
                'display_name' => 'Normal User',
                'description' => 'Basic user access'
            ],
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(
                ['name' => $role['name']],
                $role
            );
        }
    }
}