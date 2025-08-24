<?php

use App\Enums\NoteType;
use App\Models\Note;
use App\Models\Traits\UsesModal;
use Flux\Flux;
use Illuminate\Validation\Rule;
use Livewire\Volt\Component;

new class extends Component {

    use UsesModal;

    public ?Note  $note;
    public        $on;
    public string $type;
    public string $title;
    public string $content;

    public function mount(?Note $note) : void
    {
        $this->note = $note ?? new Note();
        if ($this->note->exists) {
            $this->loadNote($this->note->id);
        }

        $this->on = request()->on;

    }

    public function loadNote(int $id = 0) : void
    {
        if ($id > 0) {
            $this->note = Note::findOrFail($id);
            $this->title = $this->note->title;
            $this->type = $this->note->type;
            $this->content = $this->note->content;
        }
    }

    public function save() : void
    {

        $this->validate();

        $note_data = [
            'on'      => get_class($this->on),
            'on_id'   => $this->on->id,
            'title'   => $this->title,
            'content' => $this->content
        ];

        if ($this->note->exists) {
            // update
        } else {
            $this->note = Note::create($note_data);
        }

        // notify and dispatch
        Flux::toast("Successfully saved note", heading: "Note saved", variant: "success", position: "top-right");
        $this->dispatch('notes.index:refresh');
        $this->closeModal([
            'on',
            'note'
        ]);
    }

    public function rules() : array
    {
        return [
            'title'   => 'required|max:255',
            'content' => 'required',
            'type'    => [
                'required',
                Rule::in(NoteType::cases())
            ]
        ];
    }

}; ?>

<flux:modal
        class="w-1/2"
        variant="flyout"
        name="{{ $modal }}"
        wire:close="closeModal(['note', 'on'])"
>
    <flux:select
            variant="listbox"
            wire:model="type"
            label="Type"
    >
        @foreach(NoteType::cases() as $note_type)
            <flux:select.option>{{ $note_type->value }}</flux:select.option>
        @endforeach
    </flux:select>
    <div class="mt-4">
        <flux:input
                label="Title"
                wire:model="title"
                placeholder="Enter a title"
        />
    </div>
    <div class="mt-4">
        <flux:editor
                label="Content"
                wire:model="content"
                placeholder="Enter some content"
        />
    </div>
    <div class="mt-4 text-center">
        <flux:button
                variant="primary"
                color="emerald"
                wire:click="save"
        >
            Save Note
        </flux:button>
        <flux:button wire:click="closeModal(['note', 'on'])">
            Cancel
        </flux:button>
    </div>
</flux:modal>
