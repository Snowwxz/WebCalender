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
        Schema::table('agenda', function (Blueprint $table) {
          // Hapus kolom lama
            if (Schema::hasColumn('agenda', 'person_in_charge')) {
                $table->dropColumn('person_in_charge');
            }

            // Tambah kolom baru
            $table->text('notes')->nullable()->after('reason');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('agenda', function (Blueprint $table) {
           // Balikin kolom lama (optional type: sesuaikan dengan sebelumnya)
            $table->string('person_in_charge')->nullable();

            // Hapus kolom baru
            $table->dropColumn('notes');
        });
    }
};
