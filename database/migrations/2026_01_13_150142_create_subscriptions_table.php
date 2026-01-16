<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            
            // ✅ FIXED: Remove foreign key constraints during CREATE
            // We'll add them in a separate migration after all tables exist
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('plan_id');
            
            $table->string('stripe_subscription_id')->nullable()->unique();
            $table->string('stripe_customer_id')->nullable();
            $table->string('stripe_price_id')->nullable();
            
            $table->enum('billing_cycle', ['monthly', 'yearly'])->default('monthly');
            $table->decimal('amount', 10, 2);
            $table->string('currency')->default('usd');
            
            $table->timestamp('trial_ends_at')->nullable();
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            
            $table->enum('status', ['active', 'pending', 'canceled', 'past_due', 'trialing'])->default('pending');
            
            $table->timestamps();
            
            $table->index(['company_id', 'status']);
            $table->index('plan_id');
            $table->index('stripe_subscription_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};