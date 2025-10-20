<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('agenda', function (Blueprint $table) {
            // 🆕 Tambahkan kolom id_unit
            if (!Schema::hasColumn('agenda', 'id_unit')) {
                $table->unsignedBigInteger('id_unit')->nullable()->after('submitted_by');
                $table->foreign('id_unit')->references('id_unit')->on('units')->onDelete('set null');
            }

            // 🗑️ Hapus kolom submitted_by jika ada
            if (Schema::hasColumn('agenda', 'submitted_by')) {
                $table->dropColumn('submitted_by');
            }
        });
    }

    public function down(): void
    {
        Schema::table('agenda', function (Blueprint $table) {
            // Balikkan perubahan jika rollback
            if (Schema::hasColumn('agenda', 'id_unit')) {
                $table->dropForeign(['id_unit']);
                $table->dropColumn('id_unit');
            }

            if (!Schema::hasColumn('agenda', 'submitted_by')) {
                $table->string('submitted_by')->nullable();
            }
        });
    }
};
