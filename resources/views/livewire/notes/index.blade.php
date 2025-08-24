<?php

use App\Enums\EncounterStatus;
use App\Livewire\Traits\Sortable;
use App\Models\Encounter;
use App\Models\Note;
use App\Models\Patient;
use Illuminate\Pagination\LengthAwarePaginator;
use LaravelIdea\Helper\App\Models\_IH_Base_C;
use LaravelIdea\Helper\App\Models\_IH_Encounter_C;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Volt\Component;
use Carbon\Carbon;
use Livewire\WithPagination;

new class extends Component {

    use Sortable;

    public $on;

    public function mount($on) : void
    {
        $this->on = $on;
        $this->sort_by = 'created_at';
    }

    #[Computed]
    #[On('notes.index:refresh')]
    public function notes() : array|LengthAwarePaginator|_IH_Base_C|_IH_Encounter_C
    {
        return Note::where('notable_id', $this->on->id)
                   ->where('notable_type', $this->on::class)
                   ->orderBy($this->sort_by, $this->sort_direction)
                   ->paginate();
    }

    public function with() : array
    {
        return [
            'notes' => $this->notes
        ];
    }
}; ?>

<div>
    <div class="flex flex-row">
        <h2 class="font-bold w-full">Encounters</h2>
        <div class="flex-none">
            <flux:button
                    size="sm"
                    variant="primary"
                    color="emerald"
            >
                <flux:modal.trigger name="notes-form">
                    New Note
                </flux:modal.trigger>
            </flux:button>
        </div>
    </div>

    <livewire:notes.form
            modal="notes-form"
            :on="$on"
    />

    <flux:table :paginate="$this->notes">
        <flux:table.columns>

            <flux:table.column
                    sortable
                    :sorted="$sort_by === 'title'"
                    :direction="$sort_direction"
                    wire:click="sort('title')"
            >Title
            </flux:table.column>
            <flux:table.column
                    sortable
                    :sorted="$sort_by === 'type'"
                    :direction="$sort_direction"
                    wire:click="sort('type')"
            >Type
            </flux:table.column>
            <flux:table.column
                    sortable
                    :sorted="$sort_by === 'createdBy.full_name'"
                    :direction="$sort_direction"
                    wire:click="sort('createdBy.full_name')"
            >Created By
            </flux:table.column>
            <flux:table.column
                    sortable
                    :sorted="$sort_by === 'created_at'"
                    :direction="$sort_direction"
                    wire:click="sort('created_at')"
            >Created
            </flux:table.column>
        </flux:table.columns>
        <flux:table.rows>
            @forelse ($notes as $note)
                <flux:table.row :key="$note->id">

                    <flux:table.cell>
                        {{ $note->title }}
                    </flux:table.cell>
                    <flux:table.cell>
                        {{ $note->type }}
                    </flux:table.cell>
                    <flux:table.cell>
                        {{ $note->createdBy->full_name }}
                    </flux:table.cell>
                    <flux:table.cell>
                        {{ $note->created_at}}
                    </flux:table.cell>
                    <flux:table.cell>
                        <flux:dropdown
                                position="bottom"
                                align="end"
                        >
                            <flux:button
                                    size="sm"
                                    icon="ellipsis-horizontal"
                                    variant="ghost"
                                    inset="top bottom"
                            ></flux:button>

                        </flux:dropdown>
                    </flux:table.cell>
                </flux:table.row>
            @empty
                <flux:table.row>
                    <flux:table.cell
                            colspan="5"
                            class="text-center"
                    >
                        There are no notes for this patient.
                    </flux:table.cell>
                </flux:table.row>
            @endforelse
        </flux:table.rows>
    </flux:table>
</div>
