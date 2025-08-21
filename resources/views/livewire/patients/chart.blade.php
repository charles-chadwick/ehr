<?php

use App\Models\Patient;
use Livewire\Volt\Component;
use Carbon\Carbon;

new class extends Component {

    public Patient $patient;

    public function mount(Patient $patient) : void
    {
        $this->patient = $patient;
    }

    public function with() : array
    {
        return ['patient' => $this->patient];
    }
}; ?>

<div>
    <flux:card size="sm">
        <div class="flex">
            <div class="flex-none">
                <flux:avatar
                        class="rounded-full object-cover mx-auto w-32 h-32 mr-4"
                        src="{{ $patient->avatar }}"
                        alt="{{ $patient->full_name_extended }}"
                        title="{{ $patient->full_name_extended }}"

                />
            </div>
            <div class="w-full text-zinc-700">
                <h1 class="font-bold text-zinc-800">{{ $patient->full_name_extended }}</h1>
                @if ($patient->nickname !== "")
                    <p class="italic text-sm">"{{ $patient->nickname }}"</p>
                @endif
                <p class="text-sm">{{ $patient->gender }}</p>
                <p class="text-sm">{{ Carbon::parse($patient->date_of_birth)->format('m/d/Y') }}
                    ({{ $patient->age }})</p>
                <p class="text-sm">{{ $patient->email }}</p>
            </div>
            <div class="flex-none">
                <flux:button>
                    <flux:modal.trigger
                            name="patient-form"
                            wire:click="$dispatch('edit-patient', {id: {{ $patient->id }}})"
                    >Edit
                    </flux:modal.trigger>
                </flux:button>

                <flux:modal name="patient-form">
                    <livewire:patients.form modal="patient-form" :patient="$patient" />
                </flux:modal>
            </div>
        </div>

    </flux:card>

</div>
