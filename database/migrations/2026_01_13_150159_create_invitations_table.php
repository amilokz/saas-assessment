<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invitations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->foreignId('invited_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('role_id')->constrained()->onDelete('cascade');
            
            $table->string('email');
            $table->string('token')->unique();
            
            $table->enum('status', ['pending', 'accepted', 'expired', 'revoked'])->default('pending');
            
            $table->timestamp('expires_at');
            $table->timestamp('accepted_at')->nullable();
            
            $table->timestamps();
            
            $table->unique(['company_id', 'email']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invitations');
    }
};