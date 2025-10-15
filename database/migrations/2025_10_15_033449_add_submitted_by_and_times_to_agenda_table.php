<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('agenda', function (Blueprint $table) {
            $table->string('submitted_by')->nullable()->after('description'); // instansi yang mengajukan
            $table->time('start_time')->nullable()->after('submitted_by'); // waktu mulai
            $table->time('end_time')->nullable()->after('start_time'); // waktu selesai
        });
    }

    public function down(): void
    {
        Schema::table('agenda', function (Blueprint $table) {
            $table->dropColumn(['submitted_by', 'start_time', 'end_time']);
        });
    }
};
