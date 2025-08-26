<?php

use App\Models\Patient;
use Livewire\Volt\Component;

new class extends Component {
    public Patient $patient;
}; ?>

<div>
    <flux:card size="sm">
        <livewire:patients.details
                :patient="$patient"
                :menu="true"
        />
    </flux:card>
    <div class="mt-4">
        <flux:card size="sm">
            <h2 class="font-semibold text-sm mb-2">
                Appointments
            </h2>
            <livewire:appointments.index wire:key="{{ uniqid() }}" :patient="$patient" />
        </flux:card>
    </div>
</div>
