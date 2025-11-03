<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migrasi.
     */
    public function up(): void
    {
        Schema::table('agenda', function (Blueprint $table) {
            $table->text('reason')->nullable()->after('status')->comment('Alasan penolakan oleh admin');
        });
    }

    /**
     * Batalkan migrasi.
     */
    public function down(): void
    {
        Schema::table('agenda', function (Blueprint $table) {
            $table->dropColumn('reason');
        });
    }
};
