<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    public function run()
    {
        $roles = [
            [
                'id' => 1,
                'name' => 'super_admin',
            ],
            [
                'id' => 2,
                'name' => 'company_admin',
            ],
            [
                'id' => 3,
                'name' => 'support_user',
            ],
            [
                'id' => 4,
                'name' => 'normal_user',
            ],
        ];

        foreach ($roles as $role) {
            Role::create($role);
        }

        $this->command->info('✅ Roles seeded successfully!');
    }
}