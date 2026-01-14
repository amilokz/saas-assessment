<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Drop only the incomplete tables (not subscriptions or audit_logs)
        Schema::dropIfExists('files');
        Schema::dropIfExists('messages');
        Schema::dropIfExists('invitations');
        Schema::dropIfExists('payments');
        
        // Recreate files table
        Schema::create('files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('name');
            $table->string('original_name');
            $table->string('path');
            $table->string('mime_type');
            $table->bigInteger('size');
            $table->string('type'); // document, image, video, etc.
            $table->boolean('is_public')->default(false);
            $table->integer('download_count')->default(0);
            $table->timestamps();
            
            $table->index(['company_id', 'created_at']);
            $table->index(['type', 'created_at']);
        });
        
        // Recreate messages table
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('parent_id')->nullable()->constrained('messages')->onDelete('cascade');
            $table->string('subject')->nullable();
            $table->text('message');
            $table->string('type')->default('support'); // support, internal, etc.
            $table->string('status')->default('open'); // open, in_progress, closed
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
            
            $table->index(['company_id', 'status']);
            $table->index(['parent_id', 'created_at']);
        });
        
        // Recreate invitations table
        Schema::create('invitations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->foreignId('invited_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('role_id')->constrained()->onDelete('cascade');
            $table->string('email');
            $table->string('token')->unique();
            $table->string('status')->default('pending'); // pending, accepted, declined, expired, revoked
            $table->timestamp('expires_at');
            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('declined_at')->nullable();
            $table->timestamp('revoked_at')->nullable();
            $table->timestamps();
            
            $table->index(['company_id', 'status']);
            $table->index(['token']);
            $table->unique(['company_id', 'email', 'status']);
        });
        
        // Recreate payments table
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->foreignId('subscription_id')->nullable()->constrained()->onDelete('set null');
            $table->string('stripe_payment_intent_id')->nullable();
            $table->string('stripe_invoice_id')->nullable();
            $table->decimal('amount', 10, 2);
            $table->string('currency')->default('usd');
            $table->string('status')->default('pending'); // pending, succeeded, failed, refunded
            $table->string('payment_method')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('refunded_at')->nullable();
            $table->timestamps();
            
            $table->index(['company_id', 'status']);
            $table->unique('stripe_payment_intent_id');
            $table->unique('stripe_invoice_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('files');
        Schema::dropIfExists('messages');
        Schema::dropIfExists('invitations');
        Schema::dropIfExists('payments');
    }
};