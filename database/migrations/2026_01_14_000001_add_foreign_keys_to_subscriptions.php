<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Only add foreign keys if the tables exist
        if (Schema::hasTable('subscriptions') && 
            Schema::hasTable('companies') && 
            Schema::hasTable('plans')) {
            
            Schema::table('subscriptions', function (Blueprint $table) {
                // Add foreign key to companies
                $table->foreign('company_id')
                      ->references('id')
                      ->on('companies')
                      ->onDelete('cascade');
                
                // Add foreign key to plans (use restrict to prevent deleting plans with active subscriptions)
                $table->foreign('plan_id')
                      ->references('id')
                      ->on('plans')
                      ->onDelete('restrict');
            });
        }
    }

    public function down(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropForeign(['company_id']);
            $table->dropForeign(['plan_id']);
        });
    }
};