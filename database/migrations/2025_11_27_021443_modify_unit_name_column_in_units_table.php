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
        Schema::table('units', function (Blueprint $table) {
            // Ubah kolom unit_name dari VARCHAR menjadi TEXT
            $table->text('unit_name')->change();
        });
    }

    /**
     * Batalkan migrasi.
     */
    public function down(): void
    {
        Schema::table('units', function (Blueprint $table) {
            // Kembalikan ke VARCHAR jika rollback
            $table->string('unit_name', 255)->change();
        });
    }
};
