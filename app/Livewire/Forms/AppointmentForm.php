<?php

namespace App\Livewire\Forms;

use App\Enums\AppointmentStatus;
use App\Models\Appointment;
use Carbon\Carbon;
use Exception;
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

    public ?Appointment $appointment;

    public function setAppointment(Appointment $appointment) : void
    {
        $this->fill($appointment);
        $this->appointment = $appointment;
        $this->date = $appointment->date_and_time->format('Y-m-d');
        $this->time = $appointment->date_and_time->format('H:i');
    }

    public function save() : Appointment
    {
        $this->validate();

        try {
            return Appointment::create([
                'patient_id'    => $this->patient->id,
                'date_and_time' => Carbon::parse($this->date . ' ' . $this->time)->toDateTimeString(),
                'length'        => $this->length,
                'status'        => $this->status,
                'type'          => $this->type,
                'title'         => $this->title,
                'description'   => $this->description,
            ]);

        } catch(Exception $e) {
            logger()->error($e->getMessage());
            return new Appointment();
        }
    }

    public function update() : Appointment
    {
        $this->validate();

        try {
            $this->appointment->update([
                'patient_id'    => $this->patient->id,
                'date_and_time' => Carbon::parse($this->date . ' ' . $this->time)->toDateTimeString(),
                'length'        => $this->length,
                'status'        => $this->status,
                'type'          => $this->type,
                'title'         => $this->title,
                'description'   => $this->description,
            ]);

            return $this->appointment;

        } catch(Exception $e) {
            logger()->error($e->getMessage());
            return new Appointment();
        }
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
