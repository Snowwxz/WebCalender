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
        Schema::create('agenda_logs', function (Blueprint $table) {
         $table->id();

    $table->unsignedBigInteger('agenda_id');
    $table->foreign('agenda_id')
        ->references('id_agenda')
        ->on('agenda')
        ->onDelete('cascade');

    $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');

    $table->string('action');
    $table->text('description')->nullable();
    $table->json('old_data')->nullable();
    $table->json('new_data')->nullable();
    $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agenda_logs');
    }
};
