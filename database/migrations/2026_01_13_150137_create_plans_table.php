<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->nullable(); // ✅ Make it nullable first
            $table->string('description')->nullable();
            $table->decimal('monthly_price', 10, 2);
            $table->decimal('yearly_price', 10, 2);
            $table->integer('max_users')->nullable();
            $table->integer('max_storage_mb')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_trial')->default(false);
            $table->json('features')->nullable();
            $table->timestamps();
            
            $table->index(['is_active', 'is_trial']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};