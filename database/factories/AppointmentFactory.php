<?php

namespace Database\Factories;

use App\Models\Appointment;
use App\Models\Patient;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class AppointmentFactory extends Factory
{
    protected $model = Appointment::class;

    public function definition(): array
    {
        return [
            'date_and_time' => Carbon::now(),

            'length' => $this->faker->randomElement([15, 30, 45, 60, 90]),
            'status' => $this->faker->word(),
            'type' => $this->faker->word(),
            'title' => $this->faker->word(),
            'description' => $this->faker->text(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),

            'patient_id' => Patient::factory(),
        ];
    }
}
