<?php

use Livewire\Volt\Volt;

Volt::route('appointments/create', 'appointments.create')->name('appointments.create');
Volt::route('appointments/{appointment}/view', 'appointments.view')->name('appointments.view');