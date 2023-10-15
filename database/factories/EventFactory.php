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
            'end_date_time' => null,
            'duration' => $this->faker->numberBetween(0, 60),
        ];
    }

    /**
     * Indicate that the frequency_id should be user-defined.
     *
     * @param string $id
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function withFrequencyId(string $id)
    {
        return $this->state(function (array $_) use ($id) {
            return [
                'frequency_id' => $id
            ];
        });
    }

    /**
     * Indicate that the start_date_time should be user-defined.
     *
     * @param string $startDateTime
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function withStartDateTime(string $startDateTime)
    {
        return $this->state(function (array $_) use ($startDateTime) {
            return [
                'start_date_time' => $startDateTime
            ];
        });
    }


    /**
     * Indicate that the end_date_time should be user-defined.
     *
     * @param string $endDateTime
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function withEndDateTime(string $endDateTime)
    {
        return $this->state(function (array $_) use ($endDateTime) {
            return [
                'end_date_time' => $endDateTime
            ];
        });
    }


    /**
     * Indicate that the duration should be user-defined.
     *
     * @param int $duration
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function withDuration(string $duration)
    {
        return $this->state(function (array $_) use ($duration) {
            return [
                'duration' => $duration
            ];
        });
    }
}
