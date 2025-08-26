<?php

namespace Tests\Models;

use App\Models\Patient;
use Carbon\Carbon;

test('getAgeAttribute returns correct age when the birthday is in a past month in the current year', function () {
    $patient = new Patient([
        'date_of_birth' => Carbon::now()
                                 ->subYears(25)
                                 ->subMonths(3)
                                 ->toDateString()
    ]);

    $age = $patient->age;

    expect($age)->toBe('25 years 3 months');
});

test('getAgeAttribute returns correct age when the birthday is in the current month', function () {
    $patient = new Patient([
        'date_of_birth' => Carbon::now()
                                 ->subYears(30)
                                 ->startOfMonth()
                                 ->toDateString()
    ]);
    $age = $patient->age;

    expect($age)->toBe('30 years 0 months');
});

test('getAgeAttribute returns correct age when the birthday is in a future month in the current year', function () {
    $patient = new Patient([
        'date_of_birth' => Carbon::now()
                                 ->subYears(40)
                                 ->addMonths(2)
                                 ->toDateString()
    ]);
    $age = $patient->age;

    expect($age)->toBe('39 years 10 months');
});
