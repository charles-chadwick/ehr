<?php

namespace Tests\Models\Traits;

use App\Models\Traits\IsPerson;
use Illuminate\Database\Eloquent\Model;

test('getFullNameExtendedAttribute without middle name or suffix', function () {
    $model = new class extends Model {
        use IsPerson;

        public $prefix     = 'Dr.';
        public $first_name = 'John';
        public $last_name  = 'Smith';
        public $suffix     = '';
    };

    $result = $model->getFullNameExtendedAttribute();

    expect($result)->toBe('Dr. John Smith');
});

test('getFullNameAttribute with first and last name', function () {
    $model = new class extends Model {
        use IsPerson;

        public $first_name = 'Anna';
        public $last_name  = 'Taylor';
    };

    $result = $model->getFullNameAttribute();

    expect($result)->toBe('Anna Taylor');
});

test('getFullNameAttribute with an empty first name', function () {
    $model = new class extends Model {
        use IsPerson;

        public $first_name = '';
        public $last_name  = 'Taylor';
    };

    $result = $model->getFullNameAttribute();

    expect($result)->toBe(' Taylor');
});

test('getFullNameAttribute with a null last name', function () {
    $model = new class extends Model {
        use IsPerson;

        public $first_name = 'Anna';
        public $last_name  = null;
    };

    $result = $model->getFullNameAttribute();

    expect($result)->toBe('Anna ');
});

test('getFullNameExtendedAttribute with middle name', function () {
    $model = new class extends Model {
        use IsPerson;

        public $prefix      = 'Dr.';
        public $first_name  = 'John';
        public $middle_name = 'Alan';
        public $last_name   = 'Smith';
        public $suffix      = '';
    };

    $result = $model->getFullNameExtendedAttribute();

    expect($result)->toBe('Dr. John Alan Smith');
});

test('getFullNameExtendedAttribute with suffix', function () {
    $model = new class extends Model {
        use IsPerson;

        public $prefix     = 'Mr.';
        public $first_name = 'Adam';
        public $last_name  = 'Johnson';
        public $suffix     = 'Jr.';
    };

    $result = $model->getFullNameExtendedAttribute();

    expect($result)->toBe('Mr. Adam Johnson Jr.');
});

test('getFullNameExtendedAttribute with middle name and suffix', function () {
    $model = new class extends Model {
        use IsPerson;

        public $prefix      = 'Prof.';
        public $first_name  = 'Jane';
        public $middle_name = 'Elizabeth';
        public $last_name   = 'Doe';
        public $suffix      = 'PhD';
    };

    $result = $model->getFullNameExtendedAttribute();

    expect($result)->toBe('Prof. Jane Elizabeth Doe PhD');
});

test('getFullNameExtendedAttribute with empty prefix and suffix', function () {
    $model = new class extends Model {
        use IsPerson;

        public $prefix     = '';
        public $first_name = 'Emily';
        public $last_name  = 'Brown';
        public $suffix     = '';
    };

    $result = $model->getFullNameExtendedAttribute();

    expect($result)->toBe('Emily Brown');
});

test('getFullNameExtendedAttribute without prefix', function () {
    $model = new class extends Model {
        use IsPerson;

        public $prefix      = null;
        public $first_name  = 'Michael';
        public $middle_name = 'T.';
        public $last_name   = 'Williams';
        public $suffix      = '';
    };

    $result = $model->getFullNameExtendedAttribute();

    expect($result)->toBe('Michael T. Williams');
});