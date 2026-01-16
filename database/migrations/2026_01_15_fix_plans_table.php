<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Check if slug column exists before adding it
        if (!Schema::hasColumn('plans', 'slug')) {
            Schema::table('plans', function (Blueprint $table) {
                $table->string('slug')->nullable()->after('name');
            });
        }

        // Add missing columns if they don't exist
        $columnsToAdd = [
            'description' => 'text',
            'monthly_price' => 'decimal',
            'yearly_price' => 'decimal',
            'max_users' => 'integer',
            'max_storage_mb' => 'integer',
            'is_active' => 'boolean',
            'is_trial' => 'boolean',
            'features' => 'json',
        ];

        foreach ($columnsToAdd as $column => $type) {
            if (!Schema::hasColumn('plans', $column)) {
                if ($type === 'decimal') {
                    Schema::table('plans', function (Blueprint $table) use ($column) {
                        $table->decimal($column, 10, 2)->default(0);
                    });
                } elseif ($type === 'json') {
                    Schema::table('plans', function (Blueprint $table) use ($column) {
                        $table->json($column)->nullable();
                    });
                } else {
                    Schema::table('plans', function (Blueprint $table) use ($column, $type) {
                        $table->{$type}($column)->nullable();
                    });
                }
            }
        }

        // Add index for slug
        Schema::table('plans', function (Blueprint $table) {
            $table->index('slug');
        });
    }

    public function down(): void
    {
        // Don't drop columns in rollback to prevent data loss
    }
};