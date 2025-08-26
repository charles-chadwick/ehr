<?php

use App\Livewire\Traits\PatientStatusColor;
use App\Models\Patient;
use Livewire\Volt\Component;

new class extends Component {
    use PatientStatusColor;

    public Patient $patient;

}; ?>

<div>
    <div class="flex flex-row text-sm">
        <div class="flex-none mr-4">
            <flux:avatar
                    src="{{ $patient->avatar }}"
                    alt=""
                    class="w-14 h-14"
            />
        </div>
        <div class="flex-grow">
            <p class="font-bold">
                <a href="{{ route('patients.chart', $patient) }}">
                    {{ $patient->full_name_extended }}
                </a>
            </p>
            <p>{{ $patient->age }} ({{ $patient->dob }})</p>
            <p>{{ $patient->gender }}</p>
        </div>
        <div class="flex-none">
            <flux:badge variant="primary" color="{{ $this->statusColor($patient->status) }}">
                {{ $patient->status }}
            </flux:badge>
        </div>
    </div>
</div>
