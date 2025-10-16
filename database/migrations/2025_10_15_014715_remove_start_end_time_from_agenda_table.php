<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migrasi untuk menghapus kolom start_time dan end_time.
     */
    public function up(): void
    {
        Schema::table('agenda', function (Blueprint $table) {
            if (Schema::hasColumn('agenda', 'start_time')) {
                $table->dropColumn('start_time');
            }

            if (Schema::hasColumn('agenda', 'end_time')) {
                $table->dropColumn('end_time');
            }
        });
    }

    /**
     * Kembalikan kolom jika migrasi di-rollback.
     */
    public function down(): void
    {
        Schema::table('agenda', function (Blueprint $table) {
            $table->time('start_time')->nullable()->after('date');
            $table->time('end_time')->nullable()->after('start_time');
        });
    }
};
