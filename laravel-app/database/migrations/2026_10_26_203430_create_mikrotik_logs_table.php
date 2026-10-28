<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mikrotik_logs', function (Blueprint $table) {
            $table->id();
            $table->string('action', 30); // add_user, remove_user, disconnect, change_speed
            $table->string('card_number', 20)->nullable();
            $table->string('status', 20); // success, failed
            $table->text('message')->nullable();
            $table->string('mode', 10)->default('mock'); // mock, real
            $table->foreignId('user_id')->nullable()->constrained();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['action', 'created_at']);
            $table->index('card_number');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mikrotik_logs');
    }
};
