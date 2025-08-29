<?php

use App\Enums\AppointmentStatus;
use App\Livewire\Forms\AppointmentForm;
use App\Models\AppointmentUser;
use App\Models\Patient;
use App\Models\User;
use Flux\Flux;
use Illuminate\Support\Carbon;
use Livewire\Volt\Component;

new class extends Component {

    public AppointmentForm $form;
    public array           $selected_user_ids = [];
    public string          $modal             = "";
    public string          $message           = "";

    public function mount(Patient $patient) : void
    {
        // set the patient
        $this->form->patient = $patient;

        // set the default date and time for the next weekday at 8am
        $date = Carbon::today()
                      ->nextWeekday()
                      ->setHour(8)
                      ->setMinute(0);

        $this->form->status = AppointmentStatus::Confirmed->value;
        $this->form->date = $date->format('Y-m-d');
        $this->form->time = $date->format('H:i');
    }

    public function save() : void
    {
        // clear out the message
        $this->message = "";

        // check if the users are conflicting

        $has_conflicts = $this->form->checkScheduleConflicts($this->selected_user_ids);
        if ($has_conflicts !== null) {
            $this->message = $has_conflicts;
            return;
        }

        // try and save the appointment
        $appointment = $this->form->save();

        // check for results
        if ($appointment->exists) {

            // set the success messages
            $message = __('ehr.success_created', ['item' => __('appointments.appointment')]);
            $heading = __('ehr.success_heading');
            $variant = "success";

            // sync our users
            $appointment_users = new AppointmentUser();
            $appointment_users->syncUsers($appointment->id, $this->selected_user_ids);

            // reset the thing
            $this->form->resetExcept('patient');
            $this->message = "";
            $this->selected_user_ids = [];

            // refresh and close
            $this->dispatch('appointments.index:refresh');
            Flux::modal($this->modal)
                ->close();
        } else {
            // set the error messages
            $message = __('ehr.error_creating', ['item' => __('appointments.appointment')]);
            $heading = __('ehr.error_heading');
            $variant = "danger";
        }

        Flux::toast($message, heading: $heading, variant: $variant);

    }

}; ?>
<form
        wire:submit="save"
        name="appointment-form"
        class="min-w-1/3"
        variant="flyout"
>
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