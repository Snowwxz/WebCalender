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
           $table->string('person_in_charge', 255)->after('location');
           $table->string('involved_institution', 255)->after('person_in_charge');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('agenda', function (Blueprint $table) {
           $table->dropColumn(['person_in_charge', 'involved_institution']);
        });
    }
};
