<?php

use Livewire\Volt\Volt;

Volt::route('users', 'users.index')->name('users.index');
Volt::route('users/create', 'users.form')->name('users.create');

Volt::route('patients', 'patients.index')->name('patients.index');
Volt::route('patients/create', 'patients.form')->name('patients.create');
Volt::route('patients/{patient}', 'patients.chart')->name('patients.chart');
Volt::route('/encounters/{patient}/{encounter?}', 'encounters.form')->name('encounters.form');
