<?php

use Livewire\Volt\Volt;

/**
 * Settings
 */
Volt::route('/settings', 'settings.dashboard')
    ->name('settings.dashboard');

/**
 * Patients!
 */
Volt::route('patients/{patient}/', 'patients.chart')
    ->name('patients.chart');
Volt::route('patients', 'patients.index')
    ->name('patients.index');

/**
 * Appointments!
 */
Volt::route('appointments/create', 'appointments.create')
    ->name('appointments.create');
Volt::route('appointments/{appointment}/view', 'appointments.view')
    ->name('appointments.view');

/**
 * Encounters!
 */
Volt::route('encounters/{patient}/create', 'encounters.create')
    ->name('encounters.create');
Volt::route('encounters/{encounter}/signed', 'encounters.signed')
    ->name('encounters.view');

/**
 * Diagnosis
 */
Volt::route('diagnosis/search', 'diagnosis.search')
    ->name('diagnosis.search');
