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
        Schema::create('agenda', function (Blueprint $table) {
            $table->id('id_agenda'); // Primary key
            $table->string('agenda_name', 255);
            $table->text('description')->nullable();
            $table->date('date');
            $table->string('location', 255)->nullable();
            $table->string('status', 255)->default('pending');
            $table->unsignedBigInteger('id_user');
            $table->integer('approved_by')->nullable(); // admin yg menyetujui
            $table->timestamps();

            // Foreign key ke tabel users (opsional)
            $table->foreign('id_user')->references('id_user')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Batalkan migrasi.
     */
    public function down(): void
    {
        Schema::dropIfExists('agenda');
    }
};
