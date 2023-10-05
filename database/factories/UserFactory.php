<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => $this->faker->password(),
            'remember_token' => null,
        ];
    }

    /**
     * Indicate that the user password should be user-defined.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function withPassword(string $password)
    {
        return $this->state(function (array $attributes) use ($password) {
            return [
                'password' => Hash::make($password),
            ];
        });
    }
}
