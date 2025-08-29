<?php

namespace Database\Factories;

use App\Models\Encounter;
use App\Models\Patient;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class EncounterFactory extends Factory
{
    protected $model = Encounter::class;

    public function definition(): array
    {
        return [
            'type' => $this->faker->word(),

            'title' => $this->faker->word(),
            'date_of_service' => Carbon::now(),
            'content' => $this->faker->word(),
            'status' => $this->faker->word(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),

            'patient_id' => Patient::factory(),
        ];
    }
}
