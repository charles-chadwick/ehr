<?php

use App\Livewire\Forms\AppointmentForm;
use App\Models\User;
use Livewire\Volt\Component;

new class extends Component {

    public AppointmentForm $form;

    public function save() : void
    {
        $this->form->save();
    }
}; ?>

<div
        name="appointment-form"
        class="min-w-1/3"
        variant="flyout"
>
    @if($message != "")
        <div class="bg-red-500 text-white p-4 rounded-md">
            {{ $message }}
        </div>
    @endif

    <form wire:submit.prevent="save">
        {{-- title, type, and status --}}
        <div class="flex flex-row gap-4">
            <div class="flex-1/2">
                <flux:input
                        label="{{ __('appointments.title') }}"
                        placeholder="{{ __('appointments.title') }}"
                        wire:model="title"
                />
            </div>
            <div class="flex-1/4">
                <flux:input
                        label="{{ __('appointments.type') }}"
                        placeholder="{{ __('appointments.type') }}"
                        wire:model="type"
                />
            </div>
            <div class="flex-1/4">
                <flux:select
                        label="{{ __('appointments.status') }}"
                        placeholder="{{ __('appointments.choose_status') }}"
                        variant="listbox"
                        wire:model="status"
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
                        wire:model="date"
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
                        wire:model="time"
                        label="{{ __('appointments.time') }}"
                />
            </div>
            <div class="flex-1/4">
                <flux:input
                        type="length"
                        wire:model="length"
                        label="{{ __('appointments.length') }}"
                        placeholder="{{ __('appointments.in_minutes') }}"
                />
            </div>
        </div>

        {{-- user selection --}}
        <div class="mt-4">
            <flux:select
                    variant="listbox"
                    searchable
                    placeholder="{{ __('users.choose_users') }}"
                    wire:model.live="selected_user_ids"
                    label="{{ __('users.users') }}"
                    multiple
            >
                @foreach(User::all() as $user)
                    <flux:select.option value="{{ $user->id }}">{{ $user->full_name_extended }}</flux:select.option>
                @endforeach
            </flux:select>
            <div class="mt-2 flex flex-wrap gap-2">
                @foreach($selected_user_ids as $user_id)
                    @php $user = User::find($user_id) @endphp
                    <flux:badge
                            dismissable
                            wire:click="removeUser({{ $user_id }})"
                    >
                        {{ $user->full_name_extended }}
                    </flux:badge>
                @endforeach
            </div>
        </div>

        {{-- description --}}
        <div class="gap-4 mt-4">
            <flux:editor
                    class="h-full"
                    wire:model="description"
            />
        </div>

        {{-- submit button --}}
        <div class="px-2 mt-4 text-center">
            <flux:button
                    variant="primary"
                    color="emerald"
                    type="submit"
            >
                {{ __('ehr.save') }}
            </flux:button>
        </div>
    </form>
</div>