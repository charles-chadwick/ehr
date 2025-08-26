<?php

use App\Livewire\Forms\AppointmentForm;
use App\Enums\AppointmentStatus;
use App\Models\Patient;
use App\Models\User;
use Livewire\Volt\Component;

new class extends Component {

    public AppointmentForm $form;

    public function save() : void
    {
        $this->form->patient = Patient::find(12);
        $this->form->save();
    }
}; ?>

<div
        name="appointment-form"
        class="min-w-1/3"
        variant="flyout"
>

    @session('status')
        {{ $status }}
    @endsession
    {{-- title, type, and status --}}
    <div class="flex flex-row gap-4">
        <div class="flex-1/2">
            <flux:input
                    label="{{ __('appointments.title') }}"
                    placeholder="{{ __('appointments.title') }}"
                    wire:model="form.title"
            />
        </div>
        <div class="flex-1/4">
            <flux:input
                    label="{{ __('appointments.type') }}"
                    placeholder="{{ __('appointments.type') }}"
                    wire:model="form.type"
            />
        </div>
        <div class="flex-1/4">
            <flux:select
                    label="{{ __('appointments.status') }}"
                    placeholder="{{ __('appointments.choose_status') }}"
                    variant="listbox"
                    wire:model="form.status"
            >
                @foreach(AppointmentStatus::cases() as $appointment_status)
                    <flux:select.option>{{ $appointment_status->value }}</flux:select.option>
                @endforeach
            </flux:select>
        </div>
    </div>

    {{-- date and time --}}
    <div class="flex flex-row gap-4 mt-4">
        <div class="flex-1/2">
            <flux:date-picker
                    selectable-header
                    wire:model="form.date"
                    label="{{ __('appointments.date') }}"
            >
                <x-slot name="trigger">
                    <flux:date-picker.input />
                </x-slot>
            </flux:date-picker>
        </div>
        <div class="flex-1/4">
            <flux:input
                    type="time"
                    wire:model="form.time"
                    label="{{ __('appointments.time') }}"
            />
        </div>
        <div class="flex-1/4">
            <flux:input
                    type="length"
                    wire:model="form.length"
                    label="{{ __('appointments.length') }}"
                    placeholder="{{ __('appointments.in_minutes') }}"
            />
        </div>
    </div>

    {{-- description --}}
    <div class="gap-4 mt-4">
        <flux:editor
                class="h-full"
                wire:model="form.description"
        />
    </div>

    {{-- submit button --}}
    <div class="px-2 mt-4 text-center">
        <flux:button
                variant="primary"
                color="emerald"
                type="submit"
                wire:click="save"
        >
            {{ __('ehr.save') }}
        </flux:button>
    </div>
</div>