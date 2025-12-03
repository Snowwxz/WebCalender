<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('group_units', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_group');
            $table->unsignedBigInteger('id_unit');

            $table->foreign('id_unit')
                ->references('id_unit')
                ->on('units')
                ->onDelete('cascade');

            $table->timestamps();

            // Index untuk performa query
            $table->index('id_group');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('group_units');
    }
};
