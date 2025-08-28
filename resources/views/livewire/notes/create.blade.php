<?php

use App\Enums\NoteType;
use App\Livewire\Forms\NoteForm;
use Flux\Flux;
use Livewire\Volt\Component;

new class extends Component {

    public NoteForm $form;
    public $model;

    private function save() : void
    {
        // try and save the note
        $note = $this->form->save();

        // check for results
        if ($note->exists) {

            // set the success messages
            $message = "Successfully created note.";
            $heading = "Success";
            $variant = "success";

            // reset the thing
            $this->form->resetExcept('model');

        } else {

            // set the error messages
            $message = "Error creating note. Please contact administrator.";
            $heading = "Error";
            $variant = "danger";
        }
        Flux::toast($message, heading: $heading, variant: $variant);
    }
}; ?>

<form wire:submit="save">
    <flux:select
            label="{{ __('notes.type') }}"
            placeholder="{{ __('notes.type') }}"
            variant="listbox"
            wire:model="form.type"
    >
        @foreach(NoteType::cases() as $note_type)
            <flux:select.option>{{ $note_type->value }}</flux:select.option>
        @endforeach
    </flux:select>
    <div class="mt-4">
        <flux:input
                label="{{ __('notes.title') }}"
                placeholder="title"
                wire:model="form.title"
        />
    </div>
    <div class="mt-4">
        <flux:editor
                label="{{ __('notes.content') }}"
                placeholder="content"
                wire:model="form.content"
        />
    </div>
    <div class="mt-4 text-center">
        <flux:button
                color="emerald"
                variant="primary"
        >
            {{ __('ehr.save') }}
        </flux:button>
        <flux:button>
            {{ __('ehr.cancel') }}
        </flux:button>
    </div>
</form>
