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
    <flux:card size="sm">

        @forelse($patients as $patient)
            <div
                    wire:key="{{ $patient->id }}"
                    class="flex justify-between py-4"
            >
                <div class="w-auto">
                    {{-- patient details --}}
                    <div class="flex justify-between py-4">
                        <div class="w-24 text-center">
                            <flux:avatar
                                    class="rounded-full object-cover mx-auto"
                                    src="{{ $patient->avatar }}"
                                    alt="{{ $patient->full_name_extended }}"
                                    title="{{ $patient->full_name_extended }}"
                                    size="xl"
                            />
                            <flux:badge
                                    class="ml-1 mt-2 text-xs"
                                    color="{{ $this->statusColor($patient->status) }}"
                                    variant="outline"
                            >
                                {{ $patient->status }}
                            </flux:badge>
                        </div>

                        <div>
                            <h3 class="font-semibold text-zinc-800">
                                <a href="{{ route('patients.chart', $patient) }}">
                                    {{ $patient->full_name }}
                                </a>
                            </h3>
                            <p class="text-sm text-zinc-700">{{ $patient->gender }}</p>
                            <p class="text-sm text-zinc-700">{{ Carbon::parse($patient->date_of_birth)->format('m/d/Y') }}
                                ({{ $patient->age }})</p>
                            <p class="text-sm text-zinc-700">{{ $patient->email }}</p>
                        </div>
                    </div>
                    {{-- end details --}}

                </div>
                <div class="shrink-0">
                    <flux:dropdown
                            position="bottom"
                            align="end"
                    >
                        <flux:button
                                size="sm"
                                icon="ellipsis-horizontal"
                                inset="top bottom"
                        ></flux:button>
                        <flux:navmenu>
                            <flux:navmenu.item
                                    href="{{ route('patients.chart', $patient) }}"
                                    icon="user"
                            >
                                Go to chart
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
