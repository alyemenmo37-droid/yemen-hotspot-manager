<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
	Schema::create('subscriptions', function (Blueprint $table) {
	    $table->id();
	    $table->foreignId('user_id')->constrained()->onDelete('cascade');
	    $table->foreignId('speed_level_id')->constrained();
	    $table->enum('status', ['active', 'expired', 'cancelled']);
	    $table->date('starts_at');
	    $table->date('ends_at');
	    $table->timestamps();
	});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
