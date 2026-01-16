<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add display_name column if it doesn't exist
        if (!Schema::hasColumn('roles', 'display_name')) {
            Schema::table('roles', function (Blueprint $table) {
                $table->string('display_name')->after('name');
            });
        }

        // Add description column if it doesn't exist
        if (!Schema::hasColumn('roles', 'description')) {
            Schema::table('roles', function (Blueprint $table) {
                $table->text('description')->nullable()->after('display_name');
            });
        }
    }

    public function down(): void
    {
        // Don't remove columns in rollback to prevent data loss
    }
};