<?php

use App\Enums\NoteType;
use App\Livewire\Forms\NoteForm;
use App\Models\Note;
use Flux\Flux;
use Illuminate\Database\Eloquent\Model;
use Livewire\Attributes\On;
use Livewire\Volt\Component;

new class extends Component {

    public NoteForm $form;
    public Model    $model;
    public string   $modal             = "";

    #[On('notes.update:load')]
    public function load(Note $note) : void
    {
        $this->form->model = $this->model;
        $this->form->setNote($note);
    }

    public function update() : void
    {
        // update the note
        $note = $this->form->update();

        if ($note->exists) {
            // success
            $message = "Successfully created note.";
            $heading = "Success";
            $variant = "success";

            $this->dispatch('notes.index:refresh');
            Flux::modal($this->modal)
                ->close();
        } else {
            // error
            $message = "Error creating note. Please contact administrator.";
            $heading = "Error";
            $variant = "danger";
        }

        Flux::toast($message, heading: $heading, variant: $variant);
    }

}; ?>
<form wire:submit="update">
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
                placeholder="{{ __('notes.title') }}"
                wire:model="form.title"
        />
    </div>
    <div class="mt-4">
        <flux:editor
                label="{{ __('notes.content') }}"
                placeholder="{{ __('notes.content') }}"
                wire:model="form.content"
        />
    </div>
    <div class="mt-4 text-center">
        <flux:button
                color="emerald"
                type="submit"
                variant="primary"
        >
            {{ __('ehr.save') }}
        </flux:button>
        <flux:button>
            {{ __('ehr.cancel') }}
        </flux:button>
    </div>
</form>