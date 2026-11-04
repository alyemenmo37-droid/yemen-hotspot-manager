<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // 1. جدول الشبكات
        Schema::create('tenants', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('domain')->nullable();
            $table->timestamps();
        });

        // 2. إضافة tenant_id للجداول
        $tables = ['users', 'cards', 'categories', 'speed_levels'];
        foreach ($tables as $table) {
            Schema::table($table, function (Blueprint $table) {
                $table->foreignId('tenant_id')->nullable()->constrained()->onDelete('cascade');
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('tenants');
        // ... (حذف الأعمدة)
    }
};
