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

    <div class="grid md:grid-cols-2 gap-2 mt-2">

        {{-- appointments --}}
        <flux:card size="sm">
            <div class="flex flex-row justify-between items-center">
                <h2 class="font-semibold text-sm mb-2">
                    {{ __('appointments.appointments') }}
                </h2>
                <flux:modal class="md:max-w-1/2 md:w-1/2 sm:max-w-full sm:w-3/4" name="create-appointment">
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


        {{-- encounters --}}

        <flux:card size="sm">
            <div class="flex flex-row justify-between items-center">
                <h2 class="font-semibold text-sm mb-2">
                    {{ __('encounters.encounters') }}
                </h2>
                <flux:modal
                        class="md:max-w-1/2 md:w-1/2 sm:max-w-full sm:w-3/4"
                        name="create-encounter"
                >
                    <livewire:encounters.create
                            modal="create-encounter"
                            :patient="$patient"
                    />
                </flux:modal>
                <flux:dropdown>
                    <flux:button icon:trailing="chevron-down">Options</flux:button>
                    <flux:menu>
                        <flux:menu.item icon="plus">
                            <flux:modal.trigger name="create-encounter">
                                {{ __('ehr.create_new', ['item' => __('encounters.encounter')]) }}
                            </flux:modal.trigger>
                        </flux:menu.item>
                    </flux:menu>
                </flux:dropdown>
            </div>
            <livewire:encounters.index
                    wire:key="{{ uniqid() }}"
                    :patient="$patient"
            />
        </flux:card>

    </div>

    {{-- notes --}}
    <div class="grid md:grid-cols-2 gap-2 mt-2">
        <flux:card size="sm">
            <div class="flex flex-row justify-between items-center">
                <h2 class="font-semibold text-sm mb-2">
                    {{ __('notes.notes') }}
                </h2>
                <flux:modal
                        class="md:max-w-1/2 md:w-1/2 sm:max-w-full sm:w-3/4"
                        name="notes.create"
                >
                    <livewire:notes.create
                            modal="notes.create"
                            :model="$patient"
                    />
                </flux:modal>
                <flux:dropdown>
                    <flux:button icon:trailing="chevron-down">Options</flux:button>
                    <flux:menu>
                        <flux:menu.item icon="plus">
                            <flux:modal.trigger name="notes.create">
                                {{ __('ehr.create_new', ['item' => __('notes.note')]) }}
                            </flux:modal.trigger>
                        </flux:menu.item>
                    </flux:menu>
                </flux:dropdown>
            </div>
            <livewire:notes.index
                    wire:key="{{ uniqid() }}"
                    :model="$patient"
            />
        </flux:card>
    </div>
</div>
