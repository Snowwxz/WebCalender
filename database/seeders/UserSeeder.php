<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Unit;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        // buat akun superadmin & admin (hardcoded credentials untuk dev)
        User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@example.test',
            'email_verified_at' => now(),
            'username' => 'superadmin',
            'password' => Hash::make('superpassword'),
            'remember_token' => Str::random(10),
            'role' => 'superadmin',
            'id_unit' => null,
            'contact' => '081234567890'
        ]);

        $admin = User::create([
            'name' => 'Protokol Admin',
            'email' => 'admin.protokol@example.test',
            'email_verified_at' => now(),
            'username' => 'adminprotokol',
            'password' => Hash::make('adminpassword'),
            'remember_token' => Str::random(10),
            'role' => 'admin',
            'id_unit' => null,
            'contact' => '081298765432'
        ]);

        // buat users untuk setiap unit
        $units = Unit::all();
        foreach ($units as $unit) {
            \App\Models\User::factory()->count(3)->create([
                'id_unit' => $unit->id_unit
            ]);
        }
    }
}