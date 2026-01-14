<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
      Schema::create('companies', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('business_type')->nullable();

    $table->enum('status', [
        'pending',
        'trial_pending_approval',
        'approved',
        'rejected',
        'suspended'
    ])->default('pending');

    $table->date('trial_ends_at')->nullable();
    $table->timestamps();
});

    }

    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};

