<?php

namespace Database\Factories;

use App\Models\Diagnosis;
use App\Models\Patient;
use App\Models\PatientDx;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class PatientDxFactory extends Factory
{
    protected $model = PatientDx::class;

    public function definition() : array
    {
        return [
            'status'     => $this->faker->word(),

            'notes'      => $this->faker->word(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),

            'patient_id'   => Patient::factory(),
            'diagnosis_id' => Diagnosis::factory(),
        ];
    }
}
