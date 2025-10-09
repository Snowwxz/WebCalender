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
        Schema::create('units', function (Blueprint $table) {
            $table->unsignedBigInteger('id_unit', true); // Primary Key (auto increment)
            $table->string('unit_name', 255);
            $table->text('address')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Batalkan migrasi.
     */
    public function down(): void
    {
        Schema::dropIfExists('units');
    }
};
