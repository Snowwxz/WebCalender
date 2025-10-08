<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Agenda;
use App\Models\User;

class AgendaSeeder extends Seeder
{
    public function run()
    {
        // cari admin (penyetuju)
        $admin = User::where('role', 'admin')->first();

        // ambil semua user (role user)
        $users = User::where('role', 'user')->get();

        foreach ($users as $user) {
            // tiap user bikin 1-4 agenda
            $count = rand(1, 4);
            for ($i = 0; $i < $count; $i++) {
                $agenda = Agenda::factory()->create([
                    'id_user' => $user->id_user
                ]);

                // 50% chance jadi approved oleh admin
                if ($admin && rand(0,1) === 1) {
                    $agenda->status = 'approved';
                    $agenda->approved_by = $admin->id_user;
                    $agenda->save();
                }
            }
        }
    }
}