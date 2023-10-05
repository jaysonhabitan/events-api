<?php

namespace Database\Factories;

use App\Enum\Frequency;
use Illuminate\Database\Eloquent\Factories\Factory;

class EventFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $days = $this->faker->randomNumber(1, 30);

        return [
            'event_name' => $this->faker->name(),
            'frequency_id' => $this->faker->numberBetween(Frequency::ONCE_OFF_ID, Frequency::MONTHLY_ID),
            'start_date_time' => date('Y-m-d H:i', strtotime("+{$days} days", strtotime('2023-01-01 00:00'))),
            'end_date_time' => date('Y-m-d H:i', strtotime("+{$days} days", strtotime('2023-02-01 00:00'))),
            'duration' => $this->faker->numberBetween(0, 60),
        ];
    }

    public function withFrequencyId(string $id) {
        return $this->state(function (array $attributes) use ($id) {
            return [
                'frequency_id' => $id
            ];
        });
    }
}
