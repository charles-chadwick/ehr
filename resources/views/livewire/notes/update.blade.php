<?php

use App\Enums\NoteType;
use App\Livewire\Forms\NoteForm;
use App\Livewire\Traits\HasModal;
use App\Models\Note;
use Flux\Flux;
use Illuminate\Database\Eloquent\Model;
use Livewire\Attributes\On;
use Livewire\Volt\Component;

new class extends Component {

    use HasModal;

    public NoteForm $form;
    public Model    $model;


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
            $message = __('ehr.success_created', ['item' => __('notes.note')]);
            $heading = __('ehr.success_heading');
            $variant = "success";

            // reset the thing
            $this->form->resetExcept('model');

            // refresh and close
            $this->dispatch('notes.index:refresh');
            Flux::modal($this->modal)
                ->close();
        } else {
            // error
            $message = __('ehr.error_updating', ['item' => __('notes.note')]);
            $heading = __('ehr.error_heading');
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
        <flux:button x-on:click="Flux.modal('{{ $this->modal }}').close()">
            {{ __('ehr.cancel') }}
        </flux:button>
    </div>
</form>