<?php

use App\Models\Patient;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\On;
use Livewire\Volt\Component;

new class extends Component {

    public $patients;

    #[On("patients.index:refresh")]
    public function mount() : void
    {
        $this->patients = Patient::get();
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
            <flux:button variant="primary" color="emerald">Create New Patient</flux:button>
        </flux:modal.trigger>
    </div>
    <flux:card size="sm">

        @foreach($patients as $patient)
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
                        <div class="w-auto">
                            <h3 class="font-semibold text-zinc-800">{{ $patient->full_name_extended }}</h3>
                            <p class="text-sm text-zinc-700">{{ $patient->date_of_birth }} ({{ $patient->age }})</p>
                            <p class="text-sm text-zinc-700">{{ $patient->email }}</p>
                        </div>
                    </div>
                </div>
                <div class="shrink-0">
                    <flux:dropdown
                            position="bottom"
                            align="end"
                    >
                        <flux:button size="sm">...</flux:button>
                        <flux:navmenu>

                            <flux:navmenu.item
                                    href="#"
                                    icon="user"
                            >
                                <flux:modal.trigger
                                        name="patient-form"
                                        wire:click="$dispatch('edit-patient', {id: {{ $patient->id }}})"
                                >Account
                                </flux:modal.trigger>
                            </flux:navmenu.item>

                            <flux:navmenu.item
                                    href="#"
                                    icon="trash"
                                    variant="danger"
                            >Delete
                            </flux:navmenu.item>
                        </flux:navmenu>
                    </flux:dropdown>
                </div>
            </div>
        @endforeach
    </flux:card>
    <flux:modal name="patient-form">
        <livewire:patients.form modal="patient-form" />
    </flux:modal>
</div>
