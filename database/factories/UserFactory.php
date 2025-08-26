<?php

namespace Database\Factories;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition() : array
    {
        return [
            'role'                                       => $this->faker->randomElement(UserRole::class),

            'prefix'                                     => $this->faker->word(),
            'first_name'                                 => $this->faker->firstName(),
            'last_name'                                  => $this->faker->lastName(),
            'suffix'                                     => $this->faker->word(),
            'email'                                      => $this->faker->unique()
                                                                        ->safeEmail(),
            'email_verified_at'                          => Carbon::now(),
            'password'                                   => bcrypt($this->faker->password()),
            'remember_token'                             => Str::random(10),
            'created_at'                                 => $this->faker->word(),
            'updated_at'                                 => $this->faker->word(),
            'created_by' => User::factory(),
            'updated_by' => User::factory(),
            'deleted_by' => User::factory(),
        ];
    }
}
