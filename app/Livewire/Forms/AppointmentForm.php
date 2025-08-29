<?php

namespace App\Livewire\Forms;

use App\Enums\AppointmentStatus;
use App\Models\Appointment;
use App\Models\AppointmentUser;
use Carbon\Carbon;
use Exception;
use Illuminate\Validation\Rule;
use Livewire\Form;
use Throwable;

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

    public function setAppointment(Appointment $appointment): void
    {
        $this->resetExcept('patient');
        $this->fill($appointment);
        $this->appointment = $appointment;
        $this->date = $appointment->date_and_time->format('Y-m-d');
        $this->time = $appointment->date_and_time->format('H:i');
    }

    /**
     * @throws Throwable
     */
    public function checkScheduleConflicts(array $user_ids): ?string
    {
        // first see if we have any conflicts
        $appointment_users = new AppointmentUser;
        $has_conflict = $appointment_users->checkScheduleConflicts($user_ids,
            Carbon::parse($this->date.' '.$this->time), $this->length, isset($this->appointment) ? $this->appointment->id : null);

        // if we have a conflict, return the error message
        if (is_array($has_conflict)) {
            return view('livewire.appointments.conflict-message', ['users' => collect($has_conflict)])->render();
        }

        return null;
    }

    public function save(): Appointment
    {
        $this->validate();

        try {
            return Appointment::create([
                'patient_id' => $this->patient->id,
                'date_and_time' => Carbon::parse($this->date.' '.$this->time)
                    ->toDateTimeString(),
                'length' => $this->length,
                'status' => $this->status,
                'type' => $this->type,
                'title' => $this->title,
                'description' => $this->description,
            ]);

        } catch (Exception $e) {
            logger()->error($e->getMessage());

            return new Appointment;
        }
    }

    public function update(): Appointment
    {
        $this->validate();

        try {
            $this->appointment->update([
                'patient_id' => $this->patient->id,
                'date_and_time' => Carbon::parse($this->date.' '.$this->time)
                    ->toDateTimeString(),
                'length' => $this->length,
                'status' => $this->status,
                'type' => $this->type,
                'title' => $this->title,
                'description' => $this->description,
            ]);

            return $this->appointment;

        } catch (Exception $e) {
            logger()->error($e->getMessage());

            return new Appointment;
        }
    }

    public function rules(): array
    {
        return [
            'date' => 'required|date',
            'time' => 'required',
            'length' => 'required|integer|min:15|max:90',
            'status' => [
                'required',
                Rule::in(AppointmentStatus::cases()),
            ],
            'type' => 'required',
            'title' => 'required|max:255',
            'description' => 'nullable',
        ];
    }
}
