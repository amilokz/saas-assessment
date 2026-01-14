<?php

namespace Tests\Feature;

use App\Models\Company;
use Tests\TestCase;

class CompanyRegistrationTest extends TestCase
{
    public function test_company_registration_page_loads()
    {
        $response = $this->get('/register/company');
        $response->assertStatus(200);
        $response->assertSee('Register Your Company');
    }

    public function test_company_can_register()
    {
        $response = $this->post('/register/company', [
            'company_name' => 'Test Company',
            'admin_name' => 'Test Admin',
            'email' => 'test@company.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'business_type' => 'Technology',
            'terms' => true,
        ]);

        $response->assertRedirect('/company/dashboard');
        
        $this->assertDatabaseHas('companies', [
            'name' => 'Test Company',
            'email' => 'test@company.com',
            'status' => 'pending',
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'test@company.com',
            'name' => 'Test Admin',
        ]);
    }

    public function test_company_registration_requires_valid_data()
    {
        $response = $this->post('/register/company', [
            'company_name' => '',
            'admin_name' => '',
            'email' => 'invalid-email',
            'password' => 'short',
        ]);

        $response->assertSessionHasErrors([
            'company_name',
            'admin_name',
            'email',
            'password',
        ]);
    }

    public function test_company_registration_with_duplicate_email()
    {
        Company::factory()->create(['email' => 'existing@company.com']);

        $response = $this->post('/register/company', [
            'company_name' => 'New Company',
            'admin_name' => 'New Admin',
            'email' => 'existing@company.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'business_type' => 'Technology',
            'terms' => true,
        ]);

        $response->assertSessionHasErrors(['email']);
    }
}