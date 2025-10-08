<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition()
    {
        return [
            'name'              => $this->faker->name,
            'email'             => $this->faker->unique()->safeEmail,
            'email_verified_at' => now(),
            'username'          => $this->faker->unique()->userName,
            'password'          => Hash::make('password'), // default dev password
            'remember_token'    => Str::random(10),
            'role'              => 'user',
            'id_unit'           => null, // override di seeder
            'contact'            => $this->faker->phoneNumber,
        ];
    }
}
