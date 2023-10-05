<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class FrequencyFactory extends Factory
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
            'description' => $this->faker->text(),
            'is_enabled' => true
        ];
    }

    public function withName(string $name) {
        return $this->state(function (array $attributes) use ($name) {
            return [
                'name' => $name
            ];
        });
    }
}
