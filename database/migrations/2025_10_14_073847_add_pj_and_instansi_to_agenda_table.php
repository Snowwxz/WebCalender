<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migrasi untuk menghapus kolom category.
     */
    public function up(): void
    {
        Schema::table('agenda', function (Blueprint $table) {
            if (Schema::hasColumn('agenda', 'category')) {
                $table->dropColumn('category');
            }
        });
    }

    /**
     * Kembalikan kolom category kalau di-rollback.
     */
    public function down(): void
    {
        Schema::table('agenda', function (Blueprint $table) {
            $table->string('category', 50)->default('public');
        });
    }
};