<?php


use App\Livewire\Traits\Sortable;
use App\Models\Note;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use LaravelIdea\Helper\App\Models\_IH_Base_C;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Volt\Component;

new class extends Component {

    use Sortable;

    public Model $model;

    public function mount(Model $model) : void
    {
        $this->model = $model;
        $this->sort_by = 'created_at';
    }

    #[Computed]
    #[On('notes.index:refresh')]
    public function notes() : LengthAwarePaginator|_IH_Base_C|array
    {
        return Note::where('notes.notable_type', $this->model::class)
                   ->where('notes.notable_id', $this->model->id)
                   ->orderBy($this->sort_by, $this->sort_direction)
                   ->paginate(3);
    }

    public function with() : array
    {
        return [
            'notes' => $this->notes
        ];
    }
}; ?>

<div>
    <flux:modal
            class="md:max-w-1/2 md:w-1/2 sm:max-w-full sm:w-3/4"
            name="notes.update"
    >
        <livewire:notes.update
                modal="notes.update"
                :model="$model"
        />
    </flux:modal>
    <ul
            role="list"
            class="list-group"
    >
        @forelse($notes as $note)
            <li
                    class="list-item"
                    wire:key="note-{{ $note->id }}"
            >
                {{-- title and stuff --}}
                <div>
                    <div class="font-semibold flex flex-wrap items-center gap-x-2">

                        <a
                                class="link font-bold"
                                href="#"
                        >
                            <flux:modal.trigger
                                    name="notes.update"
                                    wire:click="$dispatch('notes.update:load', {note: {{ $note }}})"
                            >{{ $note->title }}</flux:modal.trigger>
                        </a>
                    </div>
                    <p class="font-bold">{{ $note->type }}</p>
                </div>

                <div class="text-sm text-right">
                </div>
            </li>
        @empty
            <li class="text-center">{{ __('ehr.no_records', ['items' => __('notes.notes')]) }}</li>
        @endforelse
    </ul>
    <flux:pagination :paginator="$notes" />
</div>
