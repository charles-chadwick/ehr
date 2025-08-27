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

            <div class="flex flex-row justify-between items-center">
                <h2 class="font-semibold text-sm mb-2">
                    {{ __('appointments.appointments') }}
                </h2>

                <flux:modal name="create-appointment">
                    <livewire:appointments.create
                            modal="create-appointment"
                            :patient="$patient"
                    />
                </flux:modal>

                <flux:dropdown>
                    <flux:button icon:trailing="chevron-down">Options</flux:button>

                    <flux:menu>
                        <flux:menu.item icon="plus">
                            <flux:modal.trigger name="create-appointment">
                                {{ __('appointments.schedule_new') }}
                            </flux:modal.trigger>
                        </flux:menu.item>
                    </flux:menu>
                </flux:dropdown>
            </div>

            <livewire:appointments.index
                    wire:key="{{ uniqid() }}"
                    :patient="$patient"
            />
        </flux:card>
    </div>
</div>
