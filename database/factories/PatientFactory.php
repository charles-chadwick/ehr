<?php

namespace Database\Factories;

use App\Models\Patient;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class PatientFactory extends Factory
{
    protected $model = Patient::class;

    public function definition() : array
    {
        return [
            'status' => $this->faker->word(),

            'first_name'      => $this->faker->firstName(),
            'middle_name'     => $this->faker->firstName(),
            'last_name'       => $this->faker->lastName(),
            'prefix'          => '',
            'suffix'          => '',
            'date_of_birth'   => Carbon::now(),
            'gender'          => $this->faker->randomElement([
                'Male',
                'Female'
            ]),
            'gender_identity' => $this->faker->randomElement([
                'Male',
                'Female',
                'Other'
            ]),
            'email'           => $this->faker->unique()
                                             ->safeEmail(),
            'password'        => bcrypt($this->faker->password()),
            'created_at'      => Carbon::now(),
            'updated_at'      => Carbon::now(),
        ];
    }
}
