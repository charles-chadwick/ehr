<?php


use App\Livewire\Traits\EncounterStatusColor;
use App\Livewire\Traits\Sortable;
use App\Models\Encounter;
use App\Models\Patient;
use App\Enums\EncounterStatus;
use Illuminate\Pagination\LengthAwarePaginator;
use LaravelIdea\Helper\App\Models\_IH_Base_C;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Volt\Component;
use Carbon\Carbon;
use Livewire\WithPagination;

new class extends Component {

    use Sortable, EncounterStatusColor;

    public Patient $patient;

    public function mount(Patient $patient) : void
    {
        $this->patient = $patient;
        $this->sort_by = 'date_of_service';
    }

    #[Computed]
    #[On('encounters.index:refresh')]
    public function encounters() : LengthAwarePaginator|_IH_Base_C|array
    {
        return Encounter::where('patient_id', $this->patient->id)
                        ->orderBy($this->sort_by, $this->sort_direction)
                        ->paginate(5);
    }

    public function with() : array
    {
        return [
            'encounters' => $this->encounters
        ];
    }
}; ?>

<div>
    <ul
            role="list"
            class="divide-y divide-gray-100 dark:divide-white/5  text-sm"
    >
        @forelse($encounters as $encounter)
            <li
                    wire:key="encounter-{{ $encounter->id }}"
                    class="flex flex-wrap items-center justify-between gap-x-6 gap-y-4 py-5 sm:flex-nowrap"
            >
                <div>
                    <p class="font-semibold">
                        <a
                                href="{{ route('encounters.view', $encounter) }}"
                                class="link font-bold"
                        >
                            {{ $encounter->title }}
                        </a>
                    </p>
                    <div class="mt-1 flex items-center gap-x-2">
                        <p>
                            {{ $encounter->date_of_service }}
                        </p>
                    </div>
                </div>
            </li>
        @empty
            <li class="text-center">{{ __('ehr.no_records', ['items' => __('encounters.encounters')]) }}</li>
        @endforelse
    </ul>
</div>
