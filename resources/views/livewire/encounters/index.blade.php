<?php

use App\Models\Encounter;
use App\Models\Patient;
use Livewire\Attributes\Computed;
use Livewire\Volt\Component;
use Carbon\Carbon;
use Livewire\WithPagination;

new class extends Component {

    use WithPagination;

    public string $sort_by        = 'date_of_service';
    public string $sort_direction = 'desc';

    public Patient $patient;

    public function mount(Patient $patient) : void
    {
        $this->patient = $patient;
    }

    public function sort($column) : void
    {
        if ($this->sort_by === $column) {
            $this->sort_direction = $this->sort_direction === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sort_by = $column;
            $this->sort_direction = 'asc';
        }
    }

    #[Computed]
    public function encounters()
    {
        return Encounter::where('patient_id', $this->patient->id)
                        ->orderBy($this->sort_by, $this->sort_direction)
                        ->paginate();
    }

    public function with() : array
    {
        return [
            'encounters' => $this->encounters
        ];
    }
}; ?>

<div>
    <h2 class="font-bold">Encounters</h2>
    <flux:table :paginate="$this->encounters">
        <flux:table.columns>
            <flux:table.column
                    sortable
                    :sorted="$sort_by === 'date_of_service'"
                    :direction="$sort_direction"
                    wire:click="sort('date_of_service')"
            >Date of Service
            </flux:table.column>
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
                    :sorted="$sort_by === 'status'"
                    :direction="$sort_direction"
                    wire:click="sort('status')"
            >Status
            </flux:table.column>
        </flux:table.columns>
        <flux:table.rows>
            @foreach ($encounters as $encounter)
                <flux:table.row :key="$encounter->id">
                    <flux:table.cell>
                        {{ Carbon::parse($encounter->date_of_service)->format('m/d/Y') }}
                    </flux:table.cell>
                    <flux:table.cell>
                        <a href="{{ route('encounters.form', ['patient' => $patient, 'encounter' => $encounter]) }}">
                            {{ $encounter->title }}
                        </a>

                    </flux:table.cell>
                    <flux:table.cell>
                        {{ $encounter->type }}
                    </flux:table.cell>
                    <flux:table.cell>
                        <flux:badge size="sm" color="{{ $encounter->status == 'Unsigned' ? 'emerald' : 'gray' }}">
                            {{ $encounter->status }}
                        </flux:badge>
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
                            <flux:navmenu>
                                <flux:navmenu.item
                                        href="{{ route('patients.chart', $patient) }}"
                                        icon="user"
                                >
                                    Go to chart
                                </flux:navmenu.item>
                            </flux:navmenu>
                        </flux:dropdown>
                    </flux:table.cell>
                </flux:table.row>
            @endforeach
        </flux:table.rows>
    </flux:table>
</div>
