<?php


use App\Enums\PatientStatus;
use App\Livewire\Traits\Filterable;
use App\Livewire\Traits\Sortable;
use App\Models\Patient;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use LaravelIdea\Helper\App\Models\_IH_Base_C;
use LaravelIdea\Helper\App\Models\_IH_Patient_C;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Volt\Component;

new class extends Component {

    use Sortable, Filterable;

    public string $search_term = "";

    public function mount(Patient $patient) : void
    {
        $this->patient = $patient;
        $this->sort_by = 'first_name';
        $this->sort_direction = 'asc';
    }

    #[Computed]
    #[On('patients.index:refresh')]
    public function patients() : array|_IH_Patient_C|LengthAwarePaginator|_IH_Base_C
    {
        return Patient::orderBy($this->sort_by, $this->sort_direction)
            ->when($this->search_term !== "", function (Builder $query) {
                $query->whereAny([
                    'first_name',
                    'last_name',
                    'id'
                ], 'like', trim($this->search_term).'%');
            })
            ->when($this->filter_by, function (Builder $query) {
                $query->where($this->filter_on, $this->filter_by);
            })
            ->paginate();
    }

    public function search() : void
    {
        $this->resetPage();
        $this->patients();
    }

    public function with() : array
    {
        return [
            'patients' => $this->patients
        ];
    }
}; ?>

<div>
    {{-- patient filter and search --}}
    <div class="flex justify-between mb-4">
        <flux:dropdown>
            <flux:button icon:trailing="chevron-down">Sort and Filter</flux:button>

            <flux:menu>
                <flux:menu.submenu heading="Sort by">
                    <flux:menu.radio.group>
                        <flux:menu.radio wire:click="sort('first_name')">{{ __('patients.first_name') }}</flux:menu.radio>
                        <flux:menu.radio wire:click="sort('last_name')">{{ __('patients.last_name') }}</flux:menu.radio>
                        <flux:menu.radio wire:click="sort('id')">{{ __('patients.mrn') }}</flux:menu.radio>
                    </flux:menu.radio.group>
                </flux:menu.submenu>

                <flux:menu.submenu heading="Filter">
                    <flux:menu.radio.group>
                        <flux:menu.radio wire:click="filter('status', '{{PatientStatus::Active}} ')">{{ PatientStatus::Active }}</flux:menu.radio>
                        <flux:menu.radio wire:click="filter('status', '{{PatientStatus::Inactive}} ')">{{ PatientStatus::Inactive }}</flux:menu.radio>
                        <flux:menu.radio wire:click="filter('status', '{{PatientStatus::Prospective}} ')">{{ PatientStatus::Prospective }}</flux:menu.radio>
                    </flux:menu.radio.group>
                </flux:menu.submenu>
            </flux:menu>
        </flux:dropdown>

        <div class="search-form">
            <flux:input
                icon="magnifying-glass"
                wire:model="search_term"
                wire:keyup="search"
                type="text"
                placeholder="{{ __('ehr.search') }}"
                class="w-full sm:w-auto"
            />
        </div>
    </div>

    <flux:card>
        {{-- patient list --}}
        <ul
            role="list"
            class="list-group"
        >
            @forelse($patients as $patient)
                <li
                    wire:key="patient-{{ $patient->id }}"
                    class="list-item"
                >
                    <livewire:patients.details
                        :patient="$patient"
                        :short_name="true"
                        wire:key="patient-{{ $patient->id }}-details"
                    />
                </li>
            @empty
                <li class="text-center">{{ __('ehr.no_records', ['items' => __('patients.patients')]) }}</li>
            @endforelse
        </ul>
        <div class="my-4">
            {{ $patients->links() }}
        </div>
    </flux:card>

</div>
