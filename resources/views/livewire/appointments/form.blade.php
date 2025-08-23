<?php

use App\Enums\AppointmentStatus;
use App\Models\Appointment;
use App\Models\Patient;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\Rule;
use JetBrains\PhpStorm\NoReturn;
use Livewire\Attributes\On;
use Livewire\Volt\Component;
use Flux\Flux;

new class extends Component {

    public string      $type          = "";
    public string      $date_and_time = "";
    public string      $date          = "";
    public string      $time          = "";
    public string      $length        = "";
    public string      $title         = "";
    public string      $description   = "";
    public string      $status        = "";
    public Patient     $patient;
    public Appointment $appointment;

    public function mount(?Appointment $appointment) : void
    {
        $this->loadAppointment($appointment);
        $this->patient = request()->patient;
    }

    #[On("appointment-form:refresh")]
    public function loadAppointment(Appointment $appointment) : void
    {
        if (isset($appointment->id) && $appointment->id !== null) {
            $this->appointment = $appointment;
            $this->fill($this->appointment);
        } else {

            $this->date_and_time = Carbon::now()
                                         ->format('Y-m-d');
        }
    }

    public function rules() : array
    {
        return [
            'title'         => 'required|max:255',
            'date_and_time' => 'date',
            'type'          => 'required',
            'description'   => 'nullable'
        ];
    }

    public function saveWithoutSigning() : void
    {
        $this->save();

    }

    public function saveAndSign() : void
    {
        $this->save(true);

    }

    private function save($sign = false) : void
    {
        $this->validate();

        $data = [
            'description'   => $this->description,
            'date_and_time' => $this->date_and_time,
            'title'         => $this->title,
            'type'          => $this->type,
            'status'        => $sign ? AppointmentStatus::Signed : AppointmentStatus::Unsigned,
            'singed_by'     => $sign ? auth()->user() : null,
            'signed_at'     => $sign ? Carbon::now() : null,
            'patient_id'    => $this->patient->id
        ];


        if (isset($this->appointment->id) && $this->appointment->id !== null) {

            // updating
            $this->appointment->update($data);
            // toast it up
            Flux::toast("Successfully saved appointment", heading : "Appointment saved", variant: "success",
                                                          position: "top-right");
        } else {

            // saving
            $this->appointment = Appointment::create($data);
            // toast it up
            Flux::toast("Successfully saved appointment", heading : "Appointment saved", variant: "success",
                                                          position: "top-right");
        }

        if ($sign) {
            $this->redirect(route('appointments.view', [
                'patient'     => $this->patient,
                'appointment' => $this->appointment
            ]));
        }
    }

}; ?>

<form

>
    <div class="flex flex-row gap-4">
        <div class="flex-1/2">
            <flux:input
                    label="Title"
                    wire:model="title"
                    placeholder="My Appointment"
            />
        </div>
        <div class="flex-1/4">
            <flux:input
                    label="Type"
                    placeholder="Type"
                    wire:model="type"
            />
        </div>

    </div>
    <div class="flex flex-row gap-4 mt-4">
        <div class="flex-1/2">
            <flux:date-picker
                    selectable-header
                    unavailable="2025-08-22,2025-08-24"
                    wire:model="date"
                    label="Date"
                    value="{{ $date }}"
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
                    label="Time"
                    value="{{ $time }}"
            />
        </div>
        <div class="flex-1/4">
            <flux:input
                    type="length"
                    wire:model="length"
                    label="Length"
                    placeholder="Minutes"
                    value="{{ $length }}"
            />
        </div>
    </div>
    <div class="gap-4 mt-4">
        <flux:editor
                class="h-full"
                wire:model="description"
        />
    </div>
    <div class="px-2 mt-4 text-center">
        <flux:button
                type="submit"
                color="primary"
        >
            Save Appointment
        </flux:button>
    </div>

</form>
