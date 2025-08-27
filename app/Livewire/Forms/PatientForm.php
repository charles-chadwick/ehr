<?php

namespace App\Livewire\Forms;

use App\Enums\PatientGender;
use App\Enums\PatientStatus;
use App\Models\Patient;
use Exception;
use Illuminate\Validation\Rule;
use Livewire\Form;

class PatientForm extends Form
{
    public $status;
    public $prefix;
    public $first_name;
    public $middle_name;
    public $last_name;
    public $suffix;
    public $nickname;
    public $gender;
    public $gender_identity;
    public $date_of_birth;
    public $email;
    public $password;
    public $password_confirmation;

    public ?Patient $patient;

    public function setPatient(Patient $patient) : void
    {
        $this->fill($patient);
        $this->patient = $patient;
    }

    public function save() : Patient
    {
        $this->validate();

        try {
            return Patient::create([
                'status'          => $this->status,
                'prefix'          => $this->prefix,
                'first_name'      => $this->first_name,
                'middle_name'     => $this->middle_name,
                'last_name'       => $this->last_name,
                'suffix'          => $this->suffix,
                'nickname'        => $this->nickname,
                'gender'          => $this->gender,
                'gender_identity' => $this->gender_identity,
                'date_of_birth'   => $this->date_of_birth
            ]);

        } catch (Exception $e) {
            logger()->error($e->getMessage());
            return new Patient();
        }
    }

    public function update() : Patient
    {
        $validated = $this->validate();

        try {
            $patient_data = [
                'status'          => $this->status,
                'prefix'          => $this->prefix,
                'first_name'      => $this->first_name,
                'middle_name'     => $this->middle_name,
                'last_name'       => $this->last_name,
                'suffix'          => $this->suffix,
                'nickname'        => $this->nickname,
                'gender'          => $this->gender,
                'gender_identity' => $this->gender_identity,
                'date_of_birth'   => $this->date_of_birth
            ];

            if (!empty($validated['password'] ?? null)) {
                $patient_data['password'] = bcrypt($validated['password']);
            }

            $this->patient->update($patient_data);

            return $this->patient;

        } catch (Exception $e) {
            logger()->error($e->getMessage());
            return new Patient();
        }
    }

    public function rules() : array
    {
        return [
            'status'                => [
                'required',
                Rule::in(PatientStatus::cases())
            ],
            'prefix'                => 'nullable|max:5',
            'first_name'            => 'required|max:255',
            'middle_name'           => 'required|max:255',
            'last_name'             => 'required|max:255',
            'suffix'                => 'nullable|max:5',
            'nickname'              => 'nullable|max:255',
            'gender'                => [
                'required',
                Rule::in(PatientGender::cases())
            ],
            'gender_identity'       => 'nullable|max:255',
            'date_of_birth'         => 'date|before:today',
            'password'              => 'nullable|string|min:1|max:255|confirmed',
            'password_confirmation' => 'nullable|string|min:1|max:255'
        ];
    }
}
