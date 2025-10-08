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
        Schema::table('users', function (Blueprint $table) {
            // Ubah primary key
            $table->renameColumn('id', 'id_user');

            // Tambahkan kolom baru
            $table->string('username', 255)->after('email')->nullable();
            $table->enum('role', ['superadmin', 'admin', 'user'])->default('user')->after('remember_token');
            $table->unsignedBigInteger('id_unit')->nullable()->after('role');
            $table->string('contact', 255)->nullable()->after('id_unit');

            // Tambahkan relasi ke tabel units
            $table->foreign('id_unit')->references('id_unit')->on('units')->onDelete('set null');
        });
    }

    /**
     * Batalkan perubahan migrasi.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Hapus foreign key dan kolom tambahan
            $table->dropForeign(['id_unit']);
            $table->dropColumn(['username', 'role', 'id_unit', 'contact']);

            // Kembalikan nama kolom id_user ke id
            $table->renameColumn('id_user', 'id');
        });
    }
};