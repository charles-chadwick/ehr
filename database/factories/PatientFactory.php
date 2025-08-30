<?php

namespace Database\Factories;

use App\Enums\PatientStatus;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class PatientFactory extends Factory
{
    public function definition() : array
    {
        return [
            'status' => $this->faker->randomElement(PatientStatus::class),

            'prefix'          => $this->faker->word(),
            'first_name'      => $this->faker->firstName(),
            'middle_name'     => $this->faker->name(),
            'last_name'       => $this->faker->lastName(),
            'suffix'          => $this->faker->word(),
            'nickname'        => $this->faker->word(),
            'gender'          => $this->faker->word(),
            'gender_identity' => '',
            'date_of_birth'   => Carbon::now(),
            'email'           => $this->faker->unique()
                ->safeEmail(),
            'password'        => bcrypt($this->faker->password()),
            'created_at'      => Carbon::now(),
            'updated_at'      => Carbon::now(),

            'created_by' => $this->faker->numberBetween(1, 10),
            'updated_by' => $this->faker->numberBetween(1, 10),
        ];
    }
}
