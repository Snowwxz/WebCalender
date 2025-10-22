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
            // Tambahkan kolom id_unit yang boleh null (karena onDelete set null)
            $table->unsignedBigInteger('id_unit')->nullable();

            // Tambahkan relasi ke tabel units
            $table->foreign('id_unit')
                  ->references('id_unit')
                  ->on('units')
                  ->onDelete('set null')
                  ->onUpdate('cascade');
        });
    }

    /**
     * Batalkan migrasi.
     */
    public function down(): void
    {
        Schema::table('agenda', function (Blueprint $table) {
            $table->dropForeign(['id_unit']);
            $table->dropColumn('id_unit');
        });
    }
};
