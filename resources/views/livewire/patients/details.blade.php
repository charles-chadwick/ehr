<?php

use App\Livewire\Traits\PatientStatusColor;
use App\Models\Patient;
use Livewire\Attributes\On;
use Livewire\Volt\Component;

new class extends Component {
    use PatientStatusColor;

    public Patient $patient;
    public bool  $menu = false;
    public bool    $short_name = false;

    #[On('patients.details:refresh')]
    public function with() : array
    {
        return ['patient' => $this->patient];
    }
}; ?>

<div>
    {{-- modals --}}
    <flux:modal name="patients.update">
        <livewire:patients.update
                class="md:max-w-3/4 md:w-full"
                :patient="$patient"
        />
    </flux:modal>

    <flux:modal
            class="md:max-w-3/4 md:w-full"
            name="activity-log-patient-{{ $patient->id }}"
    >
        <livewire:activity.index :object="$patient" />
    </flux:modal>

    {{-- basic --}}
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
            > (#{{$patient->id}})
                @if ($short_name)
                    {{ $patient->full_name }}
                @else
                    {{ $patient->full_name_extended }}
                @endif

            </a>
            <p>{{ $patient->gender }}
                @if($patient->gender_identity !== "")
                    ({{ $patient->gender_identity }})
                @endif

                / {{ $patient->age }} ({{ $patient->dob }})</p>
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

        {{-- option menu --}}
        <div class="flex-none text-right">
            @if($menu)
                <flux:dropdown>
                    <flux:button icon:trailing="chevron-down">{{ __('ehr.options') }}</flux:button>
                    <flux:menu>
                        <flux:menu.item icon="plus">
                            <flux:modal.trigger
                                    name="patients.update"
                                    wire:click="$dispatch('patients.update:load', {patient: {{ $patient }}})"
                            >
                                {{ __('ehr.edit', ['item' => __('patients.patient')]) }}
                            </flux:modal.trigger>
                        </flux:menu.item>
                        <flux:menu.separator />
                        <flux:menu.item icon="clock">

                            <flux:modal.trigger name="activity-log-patient-{{ $patient->id }}">
                                View Activity Log
                            </flux:modal.trigger>

                        </flux:menu.item>
                    </flux:menu>
                </flux:dropdown>
            @endif
        </div>
    </div>
</div>
