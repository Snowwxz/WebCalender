<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Agenda;

class AgendaFactory extends Factory
{
    protected $model = Agenda::class;

    public function definition()
    {
        return [
            'agenda_name' => $this->faker->sentence(4),
            'description' => $this->faker->paragraph,
            'date'        => $this->faker->dateTimeBetween('-1 month', '+2 months')->format('Y-m-d'),
            'location'    => $this->faker->city,
            'status'      => 'pending',
        ];
    }
}
