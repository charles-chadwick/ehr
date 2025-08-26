<?php

namespace App\Livewire\Forms;

use App\Enums\AppointmentStatus;
use App\Models\Appointment;
use Carbon\Carbon;
use Illuminate\Validation\Rule;
use Livewire\Form;

class AppointmentForm extends Form
{
    public $patient;
    public $date;
    public $time;
    public $length;
    public $status;
    public $type;
    public $title;
    public $description;

    public function setAppointment(Appointment $appointment) : void
    {
        $this->fill($appointment->toArray());
    }

    public function save() : void
    {
        $this->validate();
        Appointment::create([
            'patient_id'    => $this->patient->id,
            'date_and_time' => Carbon::parse($this->date . ' ' . $this->time)->toDateTimeString(),
            'length'        => $this->length,
            'status'        => $this->status,
            'type'          => $this->type,
            'title'         => $this->title,
            'description'   => $this->description,
        ]);

        session()->flash('success', 'Appointment created successfully.');
    }

    public function rules() : array
    {
        return [
            'date'        => 'required|date',
            'time'        => 'required',
            'length'      => 'required|integer|min:15|max:90',
            'status'      => [
                'required',
                Rule::in(AppointmentStatus::cases())
            ],
            'type'        => 'required',
            'title'       => 'required|max:255',
            'description' => 'nullable',
        ];
    }
}
