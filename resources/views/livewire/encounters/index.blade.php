<?php

use App\Enums\EncounterStatus;
use App\Livewire\Traits\Sortable;
use App\Models\Encounter;
use App\Models\Patient;
use Illuminate\Pagination\LengthAwarePaginator;
use LaravelIdea\Helper\App\Models\_IH_Base_C;
use LaravelIdea\Helper\App\Models\_IH_Encounter_C;
use Livewire\Attributes\Computed;
use Livewire\Volt\Component;
use Carbon\Carbon;
use Livewire\WithPagination;

new class extends Component {

    use Sortable;

    public Patient $patient;

    public function mount(Patient $patient) : void
    {
        $this->patient = $patient;
        $this->sort_by = 'date_of_service';
    }

    #[Computed]
    public function encounters() : array|LengthAwarePaginator|_IH_Base_C|_IH_Encounter_C
    {
        return Encounter::where('patient_id', $this->patient->id)
                        ->orderBy($this->sort_by, $this->sort_direction)
                        ->paginate();
    }

    public function encounterRoute(Encounter $encounter) : string
    {

        // {{  route('encounters.form', ['patient' => $patient, 'encounter' => $encounter]) }}
        if ($encounter->status == "Unsigned") {
            // we need to go to the signed encounter
            return route('encounters.form', [
                'patient'   => $this->patient,
                'encounter' => $encounter
            ]);
        } else {
            // otherwise, we will not do that
            return route('encounters.view', [
                'patient'   => $this->patient,
                'encounter' => $encounter
            ]);
        }
    }

    public function with() : array
    {
        return [
            'encounters' => $this->encounters
        ];
    }
}; ?>

<div>
    <div class="flex flex-row">
        <h2 class="font-bold w-full">Encounters</h2>
        <div class="flex-none">
            <flux:button
                    href="{{ route('encounters.form', ['patient' => $patient]) }}"
                    icon="plus"
                    size="sm"
            >
                New Encounter
            </flux:button>
        </div>
    </div>

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
            @forelse ($encounters as $encounter)
                <flux:table.row :key="$encounter->id">
                    <flux:table.cell>
                        {{ Carbon::parse($encounter->date_of_service)->format('m/d/Y') }}
                    </flux:table.cell>
                    <flux:table.cell>
                        <a href="{{ $this->encounterRoute($encounter) }}">
                            {{ $encounter->title }}
                        </a>
                    </flux:table.cell>
                    <flux:table.cell>
                        {{ $encounter->type }}
                    </flux:table.cell>
                    <flux:table.cell>
                        <flux:badge
                                size="sm"
                                color="{{ $encounter->status == EncounterStatus::Signed->value ? 'emerald' : 'gray' }}"
                        >
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
                                        href="{{ $this->encounterRoute($encounter) }}"
                                        icon="user"
                                >
                                    Go to encounter
                                </flux:navmenu.item>
                            </flux:navmenu>
                        </flux:dropdown>
                    </flux:table.cell>
                </flux:table.row>
            @empty
                <flux:table.row>
                    <flux:table.cell colspan="5" class="text-center">
                        There are no encounters for this patient.
                    </flux:table.cell>
                </flux:table.row>
            @endforelse
        </flux:table.rows>
    </flux:table>
</div>
