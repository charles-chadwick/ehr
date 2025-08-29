<?php

namespace Database\Seeders;


use App\Enums\PatientGender;
use App\Enums\PatientStatus;
use App\Models\Patient;
use App\Models\User;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Database\Seeder;
use Spatie\Activitylog\Facades\CauserResolver;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileDoesNotExist;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileIsTooBig;

class PatientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * @throws GuzzleException
     */
    public function run() : void
    {
        Patient::truncate();
        $characters = collect(json_decode(file_get_contents(database_path('src/rickandmorty_characters.json')),
            true))->whereNotIn('id', [
            3,
            5,
            9,
            10,
            23,
            29,
            41,
            59,
            108,
            109,
            111,
            121,
            139,
            146,
            149,
            163,
            187,
            196,
            218,
            21
        ])->random(300);
        $already_in = [];
        $counter = 0;

        foreach ($characters as $character) {

            if (in_array($character['name'], $already_in)) {
                continue;
            }

            $name = array_map('trim', explode(' ', $character['name']));
            if (count($name) < 2) {
                continue;
            }

            $admin = User::where('role', '!=', 'Administrator')
                         ->inRandomOrder()
                         ->first();
            CauserResolver::setCauser($admin);
            $first_name = array_shift($name);
            $last_name = array_pop($name);

            if (in_array($first_name, [
                'Mrs',
                'Ms',
                'Mr'
            ])
            ) {
                $first_name = array_shift($name);
            }

            $email = str_replace('.@', '@', strtolower("$first_name.$last_name".rand(1, 100)."@example.com"));

            $created_at = fake()->dateTimeBetween($admin->created_at, '-1 year');
            $prefix = match ($character['gender']) {
                'Male'   => 'Mr',
                'Female' => fake()->randomElement([
                    'Mrs',
                    'Ms',
                    ''
                ]),
                default  => '',
            };

            $rand = rand(0, 100);
            $status = match (true) {
                $rand <= 15 => PatientStatus::Inactive,
                $rand > 15 && $rand <= 25 => PatientStatus::Prospective,
                default     => PatientStatus::Active,
            };
            $model = Patient::factory()
                            ->create([
                                'prefix'        => $prefix,
                                'suffix'        => fake()->randomElement([
                                    'Jr.',
                                    'Sr.',
                                    'II',
                                    'III',
                                    ''
                                ]),
                                'gender' => match($character['gender']) {
                                    'Male'   => PatientGender::Male,
                                    'Female' => PatientGender::Female,
                                    default => PatientGender::NotSpecified
                                },
                                'first_name'    => $first_name,
                                'middle_name'   => count($name) > 0 ? implode(' ', $name) : '',
                                'last_name'     => $last_name === '' ? 'N/A' : $last_name,
                                'email'         => $email,
                                'password'      => bcrypt('password'),
                                'date_of_birth' => fake()->dateTimeBetween('-100 years', '-1 years'),
                                'nickname'      => "",
                                'status'        => $status,
                                'created_at'    => $created_at,
                                'updated_at'    => $created_at,
                                'created_by'    => $admin->id,
                            ]);

            $avatar_path = database_path('src/avatars/'.str_replace(' ', '-', $character['id']).'.jpeg');
            if (!file_exists($avatar_path)) {
                if (!is_dir(dirname($avatar_path))) {
                    mkdir(dirname($avatar_path), 0755, true);
                }
                try {
                    $client = new Client();
                    $response = $client->get($character['image']);
                    file_put_contents($avatar_path, $response->getBody());
                } catch (GuzzleException $e) {
                    echo $e->getMessage();
                    continue;
                }

            }

            try {
                $model->addMedia($avatar_path)
                      ->preservingOriginal()
                      ->toMediaCollection('avatars');
            } catch (FileDoesNotExist|FileIsTooBig $e) {
                echo $e->getMessage();
            }

            if ($counter++ > 200) {
                continue;
            }
            $already_in[] = $character['name'];
            echo $character['id'].",";
        }
    }
}
