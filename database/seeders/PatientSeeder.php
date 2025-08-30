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
     *
     * @throws GuzzleException
     */
    public function run() : void
    {
        Patient::truncate();
        $characters = collect(json_decode(file_get_contents(database_path('src/rickandmorty_characters.json')),
            true))
            ->whereIn('id', [
                2,3,4,8,9,13,16,18,20,22,23,27,29,41,51,53,59,75,78,83,84,93,94,99,100,101,102,106,109,113,117,127,130,132,135,140,141,142,150,152,153,158,160,163,164,165,173,177,178,181,186,195,196,203,214,218,219,222,226,228,229,231,233,241,244,247,248,250,256,257,262,263,266,267,269,271,278,279,280,286,287,289,292,307,314,318,319,321,326,328,329,333,341,344,347,348,350,355,357,358,359,360,366,369,379,384,387,388,389,390,392,401,403,410,411,412,415,420,423,426,440,441,446,458,462,464,473,475,476,485,488,489,496,497,499,500,504,505,507,508,512,515,516,519,520,521,522,524,527,541,542,544,546,549,551,558,559,572,574,578,580,584,590,594,600,602,613,618,619,620,621,631,634,635,638,639,642,649,652,653,658,660,669,670,678,684,689,692,693,696,698,700,701,702,703,706,707,708,712,714,716
            ])
            ->random(300);
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
                'Mr',
            ])
            ) {
                $first_name = array_shift($name);
            }

            $email = str_replace('.@', '@', strtolower("$first_name.$last_name".rand(1, 100).'@example.com'));

            $created_at = fake()->dateTimeBetween($admin->created_at, '-1 year');
            $prefix = match ($character['gender']) {
                'Male'   => 'Mr',
                'Female' => fake()->randomElement([
                    'Mrs',
                    'Ms',
                    '',
                ]),
                default  => '',
            };

            $rand = rand(0, 100);
            $status = match (true) {
                $rand <= 15               => PatientStatus::Inactive,
                $rand > 15 && $rand <= 25 => PatientStatus::Prospective,
                default                   => PatientStatus::Active,
            };
            $model = Patient::factory()
                ->create([
                    'prefix'        => $prefix,
                    'suffix'        => fake()->randomElement([
                        'Jr.',
                        'Sr.',
                        'II',
                        'III',
                        '',
                    ]),
                    'gender'        => match ($character['gender']) {
                        'Male'   => PatientGender::Male,
                        'Female' => PatientGender::Female,
                        default  => PatientGender::NotSpecified
                    },
                    'first_name'    => $first_name,
                    'middle_name'   => count($name) > 0 ? implode(' ', $name) : '',
                    'last_name'     => $last_name === '' ? 'N/A' : $last_name,
                    'email'         => $email,
                    'password'      => bcrypt('password'),
                    'date_of_birth' => fake()->dateTimeBetween('-100 years', '-1 years'),
                    'nickname'      => '',
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
                    $client = new Client;
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
            echo $character['id'].',';
        }
    }
}
