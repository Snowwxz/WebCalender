<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Unit;

class UnitFactory extends Factory
{
    protected $model = Unit::class;

    public function definition()
    {
        return [
            'unit_name' => $this->faker->company . ' - OPD',
            'address'   => $this->faker->address,
        ];
    }
}
