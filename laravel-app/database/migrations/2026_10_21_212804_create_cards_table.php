<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cards', function (Blueprint $table) {
            $table->id();
            $table->string('number', 20)->unique();
            $table->unsignedInteger('amount')->default(0);
            $table->string('speed', 20)->default('256k/1M');
            $table->enum('status', ['unused', 'active', 'expired', 'stopped'])
                ->default('unused');
            $table->unsignedBigInteger('data_limit_mb')->nullable();
            $table->unsignedBigInteger('data_used_mb')->default(0);
            $table->timestamp('activated_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps(); // created_at + updated_at
            
            // Indexes
            $table->index('status');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cards');
    }
};
