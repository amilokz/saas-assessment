<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        
        // Run migrations for test database
        $this->artisan('migrate:fresh');
        
        // Run seeders
        $this->artisan('db:seed');
    }

    protected function createCompany($attributes = [])
    {
        return \App\Models\Company::factory()->create($attributes);
    }

    protected function createUser($company = null, $role = 'company_admin', $attributes = [])
    {
        if (!$company) {
            $company = $this->createCompany();
        }

        $role = \App\Models\Role::where('name', $role)->first();

        return \App\Models\User::factory()->create(array_merge([
            'company_id' => $company->id,
            'role_id' => $role->id,
        ], $attributes));
    }

    protected function actingAsCompanyAdmin($company = null)
    {
        $user = $this->createUser($company, 'company_admin');
        $this->actingAs($user);
        return $user;
    }

    protected function actingAsSuperAdmin()
    {
        $company = \App\Models\Company::factory()->create([
            'name' => 'Platform Admin',
            'status' => 'approved',
        ]);

        $user = $this->createUser($company, 'super_admin');
        $this->actingAs($user);
        return $user;
    }
}