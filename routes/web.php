<?php

use Livewire\Volt\Volt;

Volt::route('users', 'users.index')->name('users.index');
Volt::route('users/create', 'users.form')->name('users.create');
