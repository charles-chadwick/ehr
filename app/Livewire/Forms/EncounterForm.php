<?php

namespace App\Livewire\Forms;

use App\Enums\EncounterStatus;
use App\Enums\EncounterType;
use App\Models\Encounter;
use App\Models\Patient;
use Exception;
use Illuminate\Validation\Rule;
use Livewire\Form;

class EncounterForm extends Form
{
    public Patient $patient;

    public $date_of_service;
    public $type;
    public $status;
    public $signed_by;
    public $signed_at;
    public $title;
    public $content;

    public ?Encounter $encounter;

    public function setEncounter(Encounter $encounter) : void
    {
        $this->resetExcept('patient');
        $this->fill($encounter);
        $this->encounter = $encounter;
    }

    private function collectData(EncounterStatus $status) : array
    {
        return [
            'patient_id'      => $this->patient->id,
            'date_of_service' => $this->date_of_service,
            'type'            => $this->type,
            'status'          => $status,
            'signed_by'       => $status == EncounterStatus::Unsigned ? null : auth()->user()->id,
            'signed_at'       => $status == EncounterStatus::Unsigned ? null : now(),
            'title'           => $this->title,
            'content'         => $this->content,
        ];
    }

    public function save(EncounterStatus $status) : Encounter
    {
        $this->validate();

        try {
            return Encounter::create($this->collectData($status));

        } catch (Exception $e) {
            logger()->error($e->getMessage());
            return new Encounter();
        }
    }

    public function update(EncounterStatus $status) : Encounter
    {
        $this->validate();

        try {
            $this->encounter->update($this->collectData($status));

            return $this->encounter;

        } catch (Exception $e) {
            logger()->error($e->getMessage());
            return new Encounter();
        }
    }

    public function rules() : array
    {
        return [
            'date_of_service' => 'required|date',
            'type'            => [
                'required',
                Rule::in(EncounterType::cases())
            ],
            'title'           => 'required|max:255',
            'content'         => 'nullable',
        ];
    }
}
