<?php

namespace App\Livewire\Forms;

use App\Enums\AppointmentStatus;
use App\Models\Appointment;
use Illuminate\Validation\Rule;
use Livewire\Form;

class AppointmentForm extends Form
{
    public $patient_id;
    public $date_and_time;
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
        Appointment::create($this->all());
    }

    public function rules() : array
    {
        return [
            'patient_id'    => 'required|exists:patients,id',
            'date_and_time' => 'required|date',
            'length'        => 'required|integer|min:15|max:90',
            'status'        => ['required', Rule::in(AppointmentStatus::cases())],
            'type'          => 'required',
            'title'         => 'required|max:255',
            'description'   => 'nullable',
        ];
    }
}
