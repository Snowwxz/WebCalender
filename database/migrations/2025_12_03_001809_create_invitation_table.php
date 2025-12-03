<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invitation', function (Blueprint $table) {
            $table->id('id_invitation');

            // Sesi boleh kosong (nullable)
            $table->string('session_name')->nullable();

            // Relasi ke agenda (wajib)
            $table->unsignedBigInteger('id_agenda');
            $table->foreign('id_agenda')
                ->references('id_agenda')
                ->on('agenda')
                ->onDelete('cascade');

            // Relasi ke group_units (wajib)
            // Note: id_group bukan foreign key karena bisa ada multiple rows dengan id_group yang sama di group_units
            $table->unsignedBigInteger('id_group');
            $table->index('id_group'); // Index untuk performa query

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invitation');
    }
};
