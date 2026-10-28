<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ⚠️ لا نضيف العمود إذا كان موجوداً
        if (! Schema::hasColumn('cards', 'speed_level_id')) {
            Schema::table('cards', function (Blueprint $table) {
                $table->foreignId('speed_level_id')
                    ->nullable()
                    ->after('speed')
                    ->constrained('speed_levels')
                    ->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('cards', 'speed_level_id')) {
            Schema::table('cards', function (Blueprint $table) {
                $table->dropForeign(['speed_level_id']);
                $table->dropColumn('speed_level_id');
            });
        }
    }
};
