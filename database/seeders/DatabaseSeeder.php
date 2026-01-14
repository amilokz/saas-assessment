<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Disable foreign key checks for the entire seeding process
        Schema::disableForeignKeyConstraints();
        
        // Clear all tables in correct order (child tables first, parent tables last)
        $tables = [
            'audit_logs',
            'files',
            'messages',
            'invitations',
            'payments',
            'subscriptions',
            'users',
            'plans',
            'companies',
            'roles',
        ];
        
        foreach ($tables as $table) {
            DB::table($table)->truncate();
        }
        
        // Re-enable foreign key checks
        Schema::enableForeignKeyConstraints();
        
        // Now seed in correct order
        $this->call([
            RoleSeeder::class,      // Parent table
            PlanSeeder::class,      // Independent table
            SuperAdminSeeder::class, // Depends on roles and companies
        ]);
    }
}