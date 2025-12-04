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

            // Group bisa kosong kalau agenda normal
            $table->unsignedBigInteger('id_group')->nullable();
            $table->foreign('id_group')
                ->references('id_group')
                ->on('group_units')
                ->onDelete('set null');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invitation');
    }
};
