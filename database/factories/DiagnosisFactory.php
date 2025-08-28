<?php

namespace Database\Factories;

use App\Models\Diagnosis;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class DiagnosisFactory extends Factory
{
    protected $model = Diagnosis::class;

    public function definition() : array
    {
        return [
            'set'         => 'ICD-11',
            
            'code'        => $this->faker->word(),
            'title'       => $this->faker->word(),
            'description' => $this->faker->text(),
            'created_at'  => Carbon::now(),
            'updated_at'  => Carbon::now(),
        ];
    }
}
