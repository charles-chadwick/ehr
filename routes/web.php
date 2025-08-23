<?php

use Livewire\Volt\Volt;

Volt::route('users', 'users.index')->name('users.index');
Volt::route('users/create', 'users.form')->name('users.create');

Volt::route('patients', 'patients.index')->name('patients.index');
Volt::route('patients/create', 'patients.form')->name('patients.create');
Volt::route('patients/{patient}', 'patients.chart')->name('patients.chart');

// encounters
Volt::route('/encounters/edit/{patient}/{encounter?}', 'encounters.form')->name('encounters.form');
Volt::route('/encounters/view/{patient}/{encounter?}', 'encounters.view')->name('encounters.view');

// appointments
Volt::route('/appointments/edit/{patient}/{appointment?}', 'appointments.form')->name('appointments.form');
Volt::route('/appointments/view/{patient}/{appointment?}', 'appointments.view')->name('appointments.view');
