<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('agenda', function (Blueprint $table) {
            if (!Schema::hasColumn('agenda', 'person_in_charge')) {
                $table->string('person_in_charge', 255)->nullable()->after('description');
            }

            if (!Schema::hasColumn('agenda', 'involved_institution')) {
                $table->string('involved_institution', 255)->nullable()->after('person_in_charge');
            }

            $table->string('status', 255)->default('pending')->change();
        });
    }

    public function down(): void
    {
        Schema::table('agenda', function (Blueprint $table) {
            if (Schema::hasColumn('agenda', 'person_in_charge')) {
                $table->dropColumn('person_in_charge');
            }

            if (Schema::hasColumn('agenda', 'involved_institution')) {
                $table->dropColumn('involved_institution');
            }
        });
    }
};
