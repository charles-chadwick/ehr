<?php

use App\Enums\AppointmentStatus;
use App\Livewire\Forms\AppointmentForm;
use App\Models\Appointment;
use App\Models\AppointmentUser;
use App\Models\Patient;
use Flux\Flux;
use Livewire\Attributes\On;
use Livewire\Volt\Component;

new class extends Component {

    public AppointmentForm $form;
    public Patient         $patient;
    public array           $selected_user_ids = [];
    public string          $modal             = "";
    public string          $message           = "";

    #[On('appointments.update:load')]
    public function load(Appointment $appointment) : void
    {
        $this->message = "";
        $this->form->patient = $this->patient;
        $this->form->setAppointment($appointment);
        $this->selected_user_ids = $appointment->users()
                                               ->pluck('user_id')
                                               ->toArray();
    }

    public function update() : void
    {
        // check if the users are conflicting
        $has_conflicts = $this->form->checkScheduleConflicts($this->selected_user_ids);
        if ($has_conflicts !== null) {
            $this->message = $has_conflicts;
            return;
        }

        // update the appointment
        $appointment = $this->form->update();

        if ($appointment->exists) {
            // success
            $message = __('ehr.success_updated', ['item' => __('appointments.appointment')]);
            $heading = __('ehr.success_heading');
            $variant = "success";

            $appointment_users = new AppointmentUser();
            $appointment_users->syncUsers($appointment->id, $this->selected_user_ids);

            $this->dispatch('appointments.index:refresh');
            Flux::modal($this->modal)
                ->close();
        } else {
            // error
            $message = __('ehr.error_updated', ['item' => __('appointments.appointment')]);
            $heading = __('ehr.error_heading');
            $variant = "danger";
        }

        Flux::toast($message, heading: $heading, variant: $variant);
    }

}; ?>
<form
        wire:submit="update"
        name="appointment-form"
        class="min-w-1/3"
        variant="flyout"
>
    {{-- message --}}
    @if($message != "")
        {!! $message !!}
    @endif

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

    <div class="mt-4">
        <livewire:users.select wire:model="selected_user_ids" />
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