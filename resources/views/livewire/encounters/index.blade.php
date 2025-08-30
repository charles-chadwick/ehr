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
            ->paginate(3);
    }

    public function with() : array
    {
        return [
            'encounters' => $this->encounters
        ];
    }
}; ?>

<div>
    <flux:modal class="xl:max-w-1/2 xl:w-1/2 sm:max-w-3/4 sm:w-3/4" name="encounters.update">
        <livewire:encounters.update
            modal="encounters.update"
            :patient="$patient"
        />
    </flux:modal>
    <ul
        role="list"
        class="list-group"
    >
        @forelse($encounters as $encounter)
            <li
                wire:key="encounter-{{ $encounter->id }}"
                class="list-item"
            >
                <div>
                    <a
                        href="#"
                        class="link font-bold"
                    >
                        <flux:modal.trigger
                            name="encounters.update"
                            wire:click="$dispatch('encounters.update:load', {encounter: {{ $encounter }}})"
                        >
                            {{ $encounter->title }}
                        </flux:modal.trigger>
                    </a>
                    <div class="mt-1 flex items-center gap-x-2">
                        <p>{{ $encounter->createdBy->full_name }} {{ $encounter->date_of_service->format(config('ehr.date_format')) }}</p>
                    </div>
                </div>
                <div>
                    <flux:badge
                        size="sm"
                        variant="primary"
                        color="{{ $this->getStatusColor($encounter->status) }}">
                        {{ $encounter->status }}
                    </flux:badge>

                </div>
            </li>
        @empty
            <li class="text-center">{{ __('ehr.no_records', ['items' => __('encounters.encounters')]) }}</li>
        @endforelse
        <flux:pagination :paginator="$encounters" />
    </ul>
</div>
