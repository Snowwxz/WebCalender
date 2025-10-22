<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('agenda', function (Blueprint $table) {
            // Tambahkan kolom id_unit (nullable agar tidak error saat awal migrasi)
            $table->unsignedBigInteger('id_unit')->nullable()->after('id');

            // Tambahkan foreign key ke tabel units
            $table->foreign('id_unit')
                ->references('id')
                ->on('units')
                ->onDelete('set null'); // jika unit dihapus, otomatis id_unit di agenda jadi null
        });
    }

    public function down(): void
    {
        Schema::table('agenda', function (Blueprint $table) {
            // Hapus foreign key dan kolom id_unit saat rollback
            $table->dropForeign(['id_unit']);
            $table->dropColumn('id_unit');
        });
    }
};
