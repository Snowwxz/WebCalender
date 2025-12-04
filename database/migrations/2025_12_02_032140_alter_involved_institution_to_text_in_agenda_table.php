<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('agenda', function (Blueprint $table) {
            $table->text('involved_institution')->change();
        });
    }

    public function down(): void
    {
        Schema::table('agenda', function (Blueprint $table) {
            // Kembalikan ke string 500 jika di-rollback
            $table->string('involved_institution', 500)->change();
        });
    }
};
