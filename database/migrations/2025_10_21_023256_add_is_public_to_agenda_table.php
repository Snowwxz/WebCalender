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
              // Boolean: true = publik, false = privat
            $table->boolean('is_public')
                ->default(true)
                ->after('status'); // taruh setelah kolom status (bisa ubah sesuai kebutuhan)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('agenda', function (Blueprint $table) {
            $table->dropColumn('is_public');
        });
    }
};
