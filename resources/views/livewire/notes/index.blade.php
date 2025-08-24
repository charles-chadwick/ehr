<?php

use App\Enums\NoteType;
use App\Models\Note;
use Livewire\Attributes\On;
use Livewire\Volt\Component;

new class extends Component {

    public $on;
    public NoteType $type;
    public $notes;

    public function mount($on) : void
    {
        $this->on = $on;
        $this->loadNotes();
    }

    #[On("notes.index:refresh")]
    public function loadNotes() : void
    {
        $this->notes = Note::where('on', $this->on::class)
                           ->where('type', $this->type)
                           ->get();
    }

}; ?>

<div>
    <div class="flex gap-2 justify-between">
        <h2 class="font-bold w-full">Admin Note</h2>
        <div class="flex-none">
            <flux:button
                    size="sm"
                    variant="primary"
                    color="emerald"
            >
                <flux:modal.trigger name="note-form">Add Note</flux:modal.trigger>
            </flux:button>
        </div>
    </div>
    <livewire:notes.form
            modal="note-form"
            :on="$on"
            :type="$type->value"
    />
    @forelse($notes as $note)
        I'm a note.
    @empty
        <p class="text-sm py-2">There are no {{ $type }} notes yet.</p>
    @endforelse
</div>
