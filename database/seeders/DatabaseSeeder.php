<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\Patient;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run() : void
    {
//        (new UserSeeder())->run();
//        (new PatientSeeder())->run();
        (new DxSeeder())->run();
    }
}
