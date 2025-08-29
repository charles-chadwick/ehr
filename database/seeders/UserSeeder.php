<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Database\Seeder;
use Spatie\Activitylog\Facades\CauserResolver;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileDoesNotExist;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileIsTooBig;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @throws GuzzleException
     */
    public function run(): void
    {
        User::truncate();
        $admin = User::factory()
            ->create([
                'first_name' => 'John',
                'last_name' => 'Doe',
                'role' => UserRole::Admin,
                'prefix' => 'Mr.',
                'suffix' => 'Jr',
                'email' => 'test@example.com',
                'created_at' => '2020-01-01 00:00:00',
            ]);

        $characters = collect(json_decode(file_get_contents(database_path('src/rickandmorty_characters.json')),
            true))->whereIn('id', [
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
                21,
            ]);
        $already_in = [];
        $counter = 0;
        CauserResolver::setCauser($admin);
        foreach ($characters as $character) {

            if (in_array($character['name'], $already_in)) {
                continue;
            }

            $role = match (true) {
                $counter <= 4 => UserRole::Doctor,
                $counter <= 10 => UserRole::Nurse,
                $counter <= 15 => UserRole::MedicalAssistant,
                default => UserRole::Staff
            };

            if ($counter === 40) {
                exit('Done');
            }

            $name = array_map('trim', explode(' ', $character['name']));

            if (count($name) < 2) {
                continue;
            }
            $first_name = array_shift($name);
            $last_name = array_pop($name);

            $suffix = match (true) {
                $role == UserRole::Doctor => fake()->randomElement([
                    'MD',
                    'DO',
                    'DPM',
                ]),
                $role == UserRole::Nurse => fake()->randomElement([
                    'RN',
                    'ARNP',
                    'NP',
                ]),
                default => ''
            };

            $email = str_replace('.@', '@', strtolower("$first_name.$last_name".rand(1, 100).'@example.com'));

            $created_at = fake()->dateTimeBetween($admin->created_at, '-1 year');
            $model = User::factory()
                ->create([
                    'prefix' => $role == UserRole::Doctor ? 'Dr.' : '',
                    'suffix' => $suffix,
                    'first_name' => $first_name,
                    'last_name' => $last_name === '' ? 'N/A' : $last_name,
                    'role' => $role,
                    'email' => $email,
                    'password' => bcrypt('password'),
                    'created_at' => $created_at,
                    'updated_at' => $created_at,
                ]);

            $avatar_path = database_path('src/avatars/'.str_replace(' ', '-', $character['id']).'.jpeg');
            if (! file_exists($avatar_path)) {
                if (! is_dir(dirname($avatar_path))) {
                    mkdir(dirname($avatar_path), 0755, true);
                }
                try {
                    $client = new Client;
                    $response = $client->get($character['image']);
                    file_put_contents($avatar_path, $response->getBody());
                } catch (GuzzleException $e) {
                    echo $e->getMessage();

                    continue;
                }

            }

            $counter++;
            try {
                $model->addMedia($avatar_path)
                    ->preservingOriginal()
                    ->toMediaCollection('avatars');
            } catch (FileDoesNotExist|FileIsTooBig $e) {
                echo $e->getMessage();
            }

            $already_in[] = $character['name'];
            echo $character['id'].',';
        }
    }
}
