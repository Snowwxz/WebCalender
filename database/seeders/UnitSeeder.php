<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Unit;

class UnitSeeder extends Seeder
{
    public function run()
    {
        // buat 5 unit
        Unit::factory()->count(5)->create();
    }
}
