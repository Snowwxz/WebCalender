<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Agenda;
use App\Models\User;
use App\Models\Unit;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     * Includes unit, user, and agenda dummy data for dev environment.
     */
    public function run(): void
    {
        // Optional: clean tables before seeding (for dev/testing only)
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Agenda::truncate();
        User::truncate();
        Unit::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Call individual seeders
        $this->call([
            UnitSeeder::class,
            UserSeeder::class,
            AgendaSeeder::class,
        ]);
    }
}