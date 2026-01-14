<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            // Add admin_name after name
            $table->string('admin_name')->after('name');
            
            // Add email after admin_name
            $table->string('email')->after('admin_name');
            
            // Make email unique
            $table->unique('email');
        });
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn(['admin_name', 'email']);
        });
    }
};