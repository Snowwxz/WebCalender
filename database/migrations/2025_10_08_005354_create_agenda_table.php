<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('agenda', function (Blueprint $table) {
            $table->increments('id_agenda');
            $table->string('agenda_name', 255);
            $table->text('description')->nullable();
            $table->date('date');
            $table->string('location', 255)->nullable();
            $table->string('status', 255)->nullable();
            $table->unsignedBigInteger('id_user');
            $table->integer('approved_by')->nullable();
            $table->timestamps(); // created_at & updated_at

            // Jika ada relasi ke tabel users
            $table->foreign('id_user')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('agenda');
    }
};