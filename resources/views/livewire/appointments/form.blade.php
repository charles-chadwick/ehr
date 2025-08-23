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
    public string      $unavailable   = "2025-08-22,2025-08-24";
    public Patient     $patient;
    public Appointment $appointment;

    public function mount(?Appointment $appointment) : void
    {
        $this->date = Carbon::tomorrow()->format('Y-m-d');
        $this->time = "08:00";

        $this->appointment = $appointment;

        if (isset($appointment->id) && $appointment->id != null) {
            $this->loadAppointment($this->appointment->id);
        }
        $this->patient = request()->patient;
    }

    #[On("edit-appointment")]
    public function loadAppointment($id) : void
    {
        if ($id > 0) {
            $this->appointment = Appointment::findOrFail($id);
            $this->fill($this->appointment);
        }
    }

    public function rules() : array
    {
        return [

            'date'        => 'required|date',
            'time'        => 'required',
            'length'      => 'required|integer',
            'status'      => [
                'required',
                Rule::in(AppointmentStatus::cases())
            ],
            'title'       => 'required',
            'type'        => 'required',
            'description' => 'nullable'
        ];
    }

    public function save() : void
    {
        $validated = $this->validate();
        list($hour, $minute) = explode(':', $validated['time']);
        $data = [
            'patient_id'    => $this->patient->id,
            'date_and_time' => Carbon::parse($this->date)->addHours((int) $hour)->addMinutes((int) $minute),
            'length'        => $this->length,
            'status'        => $this->status,
            'type'          => $this->type,
            'title'         => $this->title,
            'description'   => $this->description,
        ];

        if (isset($this->appointment->id) && $this->appointment->id !== null) {

            // updating
            $this->appointment->update($data);
        } else {

            // saving
            $this->appointment = Appointment::create($data);
        }

        Flux::toast("Successfully saved appointment", heading : "Appointment saved", variant: "success",
                                                      position: "top-right");

        $this->dispatch('appointment.index:refresh');
    }

}; ?>

<form
        wire:submit.prevent="save"
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
        <div class="flex-1/4">
            <flux:select
                    label="Status"
                    variant="listbox"
                    placeholder="Choose Status"
                    wire:model="status"
            >
                @foreach(AppointmentStatus::cases() as $appointment_status)
                    <flux:select.option>{{ $appointment_status }}</flux:select.option>
                @endforeach
            </flux:select>
        </div>

    </div>
    <div class="flex flex-row gap-4 mt-4">
        <div class="flex-1/2">
            <flux:date-picker
                    selectable-header
                    unavailable="{{ $unavailable }}"
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
