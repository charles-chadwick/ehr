<?php

use App\Enums\PatientStatus;
use App\Models\Patient;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\On;
use Livewire\Volt\Component;
use Carbon\Carbon;

new class extends Component {

    public $patients;

    #[On("patients.index:refresh")]
    public function mount() : void
    {
        $this->patients = Patient::get();
    }

    public function statusColor($status) : string
    {
        return match ($status) {
            PatientStatus::Prospective->value => 'purple',
            PatientStatus::Inactive->value    => 'gray',
            PatientStatus::Active->value      => 'emerald',
        };
    }

    public function with() : array
    {
        return ['patients' => $this->patients];
    }
}; ?>

<div>
    <div class="flex justify-between font-bold text-zinc-700 mb-4">
        <h3 class="font-bold text-2xl">Patients</h3>
        <flux:modal.trigger name="patient-form">
            <flux:button
                    variant="primary"
                    color="emerald"
            >Create New Patient
            </flux:button>
        </flux:modal.trigger>

    </div>

    <livewire:patients.form modal="patient-form" />

    <flux:card size="sm">

        @forelse($patients as $patient)
            <div
                    wire:key="{{ $patient->id }}"
                    class="flex justify-between px-2 py-4"
            >
                <div class="w-auto">
                    <div class="flex justify-between px-2 py-4">
                        <flux:avatar
                                class="flex-none rounded-full object-cover mr-4"
                                src="{{ $patient->avatar }}"
                                alt="{{ $patient->full_name_extended }}"
                                title="{{ $patient->full_name_extended }}"
                                size="md"
                        />
                        <div class="w-auto text-sm text-zinc-700">
                            <h3 class="font-semibold">
                                <a href="{{ route('patients.chart', $patient) }}">
                                    {{ $patient->full_name_extended }}
                                </a>
                            </h3>
                            <p>#{{ $patient->id }}
                               : {{ $patient->gender }} {{ Carbon::parse($patient->date_of_birth)->format('m/d/Y') }}
                               ({{ $patient->age }})</p>
                        </div>
                    </div>
                </div>
                <div class="shrink-0">
                    <flux:dropdown
                            position="bottom"
                            align="end"
                    >
                        <flux:button
                                size="sm"
                                variant="ghost"
                                icon="ellipsis-horizontal"
                        ></flux:button>
                        <flux:navmenu>
                            <flux:navmenu.item href="{{ route('patients.chart', $patient) }}">Go to Chart
                            </flux:navmenu.item>
                        </flux:navmenu>
                    </flux:dropdown>
                </div>
            </div>
        @empty
            <div class="text-center text-zinc-700">
                There are no patients in the system.
            </div>
        @endforelse
    </flux:card>

</div>
