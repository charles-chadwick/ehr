<?php

namespace App\Livewire\Forms;

use App\Enums\NoteType;
use App\Models\Note;
use App\Models\Patient;
use Exception;
use Illuminate\Validation\Rule;
use Livewire\Form;

class NoteForm extends Form
{
    public Patient $model;

    public $type;
    public $title;
    public $content;

    public ?Note $note;

    public function setNote(Note $note) : void
    {
        $this->resetExcept('model');
        $this->fill($note);
        $this->note = $note;
    }

    public function save() : Note
    {
        $this->validate();

        try {
            return Note::create($this->collectData());

        } catch (Exception $e) {
            logger()->error($e->getMessage());
            return new Note();
        }
    }

    public function update() : Note
    {
        $this->validate();

        try {
            $this->note->update($this->collectData());

            return $this->note;

        } catch (Exception $e) {
            logger()->error($e->getMessage());
            return new Note();
        }
    }

    private function collectData() : array
    {
        return [
            'notable_type'    => get_class($this->model),
            'notable_id'      => $this->model->id,
            'type'            => $this->type,
            'title'           => $this->title,
            'content'         => $this->content,
        ];
    }

    public function rules() : array
    {
        return [
            'type'            => [
                'required',
                Rule::in(NoteType::cases())
            ],
            'title'           => 'required|max:255',
            'content'         => 'nullable',
        ];
    }
}
