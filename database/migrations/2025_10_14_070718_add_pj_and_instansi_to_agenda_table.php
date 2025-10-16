<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tambah kolom baru ke tabel agenda
     */
    public function up(): void
    {
        Schema::table('agenda', function (Blueprint $table) {
            $table->string('person_in_charge')->nullable()->after('description'); // setelah kolom description (ubah jika beda)
            $table->string('involved_institution')->nullable()->after('person_in_charge');
        });
    }

    /**
     * Hapus kolom jika rollback
     */
    public function down(): void
    {
        Schema::table('agenda', function (Blueprint $table) {
            $table->dropColumn(['person_in_charge', 'involved_institution']);
        });
    }
};
