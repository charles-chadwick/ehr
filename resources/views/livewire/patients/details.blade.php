<?php

use App\Livewire\Traits\PatientStatusColor;
use App\Models\Patient;
use Livewire\Attributes\On;
use Livewire\Volt\Component;

new class extends Component {
    use PatientStatusColor;

    public Patient $patient;
    public string  $menu = "";

    #[On('patients.details:refresh')]
    public function with() : array
    {
        return ['patient' => $this->patient];
    }
}; ?>

<div>
    <flux:modal name="patient-update">
        <livewire:patients.update :patient="$patient" />
    </flux:modal>
    <div class="flex flex-row text-sm">
        <div class="flex-none mr-2">
            <flux:avatar
                    src="{{ $patient->avatar }}"
                    alt=""
                    class="w-16 h-16"
            />
        </div>
        <div class="flex-grow">
            <a
                    href="{{ route('patients.chart', $patient) }}"
                    class="font-semibold"
            >
                {{ $patient->full_name_extended }}
            </a>
            <p>{{ $patient->gender }} / {{ $patient->age }} ({{ $patient->dob }})</p>
            <p>
                <flux:badge
                        class="h-5"
                        size="sm"
                        variant="primary"
                        color="{{ $this->statusColor($patient->status) }}"
                >
                    {{ $patient->status }}
                </flux:badge>
            </p>
        </div>
        <div class="flex-none text-right">
            @if($menu !== "")
                <flux:dropdown>
                    <flux:button icon:trailing="chevron-down">{{ __('ehr.options') }}</flux:button>
                    <flux:menu>
                        <flux:menu.item icon="plus">
                            <flux:modal.trigger
                                    name="patient-update"
                                    wire:click="$dispatch('patients.update:load', {patient: {{ $patient }}})"
                            >
                                {{ __('ehr.edit', ['item' => __('patients.patient')]) }}
                            </flux:modal.trigger>
                        </flux:menu.item>
                    </flux:menu>
                </flux:dropdown>
            @endif
        </div>
    </div>
</div>
