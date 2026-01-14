<?php

namespace Tests\Feature;

use App\Models\Plan;
use App\Models\Subscription;
use Tests\TestCase;

class SubscriptionTest extends TestCase
{
    public function test_company_can_view_subscription_page()
    {
        $user = $this->actingAsCompanyAdmin();

        $response = $this->get('/company/subscription');
        
        $response->assertStatus(200);
        $response->assertSee('Subscription Management');
    }

    public function test_company_can_view_available_plans()
    {
        $user = $this->actingAsCompanyAdmin();

        // Create test plans
        Plan::factory()->create(['name' => 'Basic']);
        Plan::factory()->create(['name' => 'Pro']);

        $response = $this->get('/company/subscription');
        
        $response->assertStatus(200);
        $response->assertSee('Basic');
        $response->assertSee('Pro');
    }

    public function test_company_with_trial_cannot_subscribe()
    {
        $company = \App\Models\Company::factory()->create([
            'status' => 'trial_pending_approval',
        ]);
        
        $user = $this->createUser($company, 'company_admin');
        $this->actingAs($user);

        $plan = Plan::factory()->create();

        $response = $this->post('/company/subscription', [
            'plan_id' => $plan->id,
            'billing_cycle' => 'monthly',
        ]);

        $response->assertSessionHas('error');
    }

    public function test_approved_company_can_subscribe()
    {
        $company = \App\Models\Company::factory()->create([
            'status' => 'approved',
        ]);
        
        $user = $this->createUser($company, 'company_admin');
        $this->actingAs($user);

        $plan = Plan::factory()->create();

        $response = $this->post('/company/subscription', [
            'plan_id' => $plan->id,
            'billing_cycle' => 'monthly',
        ]);

        // Note: In a real test with Stripe, this would need mocking
        $response->assertStatus(200);
    }
}