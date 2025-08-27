<?php

use Livewire\Volt\Volt;

Volt::route('appointments/create', 'appointments.create')
    ->name('appointments.create');
Volt::route('appointments/{appointment}/view', 'appointments.view')
    ->name('appointments.view');
Volt::route('patients/{patient}/', 'patients.chart')
    ->name('patients.chart');
Volt::route('patients', 'patients.index')
    ->name('patients.index');

Volt::route('encounters/{patient}/create', 'encounters.create')->name('encounters.create');
Volt::route('encounters/{encounter}/signed', 'encounters.signed')->name('encounters.view');