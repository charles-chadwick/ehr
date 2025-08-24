<?php

use App\Models\Patient;
use Livewire\Volt\Component;
use Carbon\Carbon;
use App\Enums\NoteType;

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
    {{-- header --}}
    <flux:card size="sm">
        <div class="flex">
            <div class="w-full">
                <livewire:patients.details :patient="$patient" />
            </div>
            {{-- patient menu --}}
            <div class="flex-none">
                <div class="mb-2">
                    <flux:button
                            size="sm"
                            class="w-full"
                            variant="primary"
                            color="emerald"
                    >
                        <flux:modal.trigger
                                name="patient-form"
                                wire:click="$dispatch('edit-patient', {id: {{ $patient->id }}})"
                        >{{ __('chart.edit_patient') }}
                        </flux:modal.trigger>
                    </flux:button>
                </div>
                <div>
                    <flux:button
                            size="sm"
                            variant="primary"
                            color="emerald"
                            class="w-full"
                    >
                        {{ __('chart.send_message') }}
                    </flux:button>
                </div>
            </div>
        </div>

        {{-- form --}}
        <flux:modal
                name="patient-form"
                variant="flyout"
        >
            <livewire:patients.form
                    modal="patient-form"
                    :patient="$patient"
            />
        </flux:modal>
    </flux:card>

    {{-- encounters --}}
    <flux:card
            size="sm"
            class="mt-4"
    >
        <livewire:encounters.index :patient="$patient" />
    </flux:card>

    {{-- appointments --}}
    <flux:card
            size="sm"
            class="mt-4"
    >
        <livewire:appointments.index :patient="$patient" />
    </flux:card>

    {{-- notes --}}
    <div class="flex flex-col gap-4"></div>
    <flux:card
            size="sm"
            class="mt-4"
    >

        <livewire:notes.index
                :on="$patient"
                :type="NoteType::Admin"
        />
    </flux:card>
</div>
