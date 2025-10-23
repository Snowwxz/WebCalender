<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
<<<<<<<< HEAD:database/migrations/2025_10_15_025559_add_time_to_agenda_table.php
     * Run the migrations.
========
     * Tambahkan kolom start_time dan end_time ke tabel agenda.
>>>>>>>> 2b1c2ae (update code v2):database/migrations/2025_10_15_065144_add_start_end_time_to_agenda_table.php
     */
    public function up(): void
    {
        Schema::table('agenda', function (Blueprint $table) {
<<<<<<<< HEAD:database/migrations/2025_10_15_025559_add_time_to_agenda_table.php
             $table->time('time')->nullable()->after('date');
========
            $table->time('start_time')->nullable()->after('date');
            $table->time('end_time')->nullable()->after('start_time');
>>>>>>>> 2b1c2ae (update code v2):database/migrations/2025_10_15_065144_add_start_end_time_to_agenda_table.php
        });
    }

    /**
<<<<<<<< HEAD:database/migrations/2025_10_15_025559_add_time_to_agenda_table.php
     * Reverse the migrations.
========
     * Hapus kolom jika rollback.
>>>>>>>> 2b1c2ae (update code v2):database/migrations/2025_10_15_065144_add_start_end_time_to_agenda_table.php
     */
    public function down(): void
    {
        Schema::table('agenda', function (Blueprint $table) {
<<<<<<<< HEAD:database/migrations/2025_10_15_025559_add_time_to_agenda_table.php
              $table->dropColumn('time');
========
            $table->dropColumn(['start_time', 'end_time']);
>>>>>>>> 2b1c2ae (update code v2):database/migrations/2025_10_15_065144_add_start_end_time_to_agenda_table.php
        });
    }
};
