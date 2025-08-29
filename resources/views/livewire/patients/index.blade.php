<?php


use App\Livewire\Traits\Sortable;
use App\Models\Patient;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use LaravelIdea\Helper\App\Models\_IH_Base_C;
use LaravelIdea\Helper\App\Models\_IH_Appointment_C;
use LaravelIdea\Helper\App\Models\_IH_Patient_C;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Volt\Component;
use Carbon\Carbon;
use Livewire\WithPagination;
use Ramsey\Collection\Collection;

new class extends Component {

    use Sortable;

    public string $search_term = "";

    public function mount(Patient $patient) : void
    {
        $this->patient = $patient;
        $this->sort_by = 'id';
        $this->sort_direction = 'asc';
    }

    #[Computed]
    #[On('patients.index:refresh')]
    public function patients() : array|_IH_Patient_C|LengthAwarePaginator|_IH_Base_C
    {
        return Patient::orderBy($this->sort_by, $this->sort_direction)
                      ->when($this->search_term !== "", function (Builder $query) {
                          $query->whereAny(['first_name', 'last_name', 'id'], 'like', trim($this->search_term).'%');
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
    {{-- patient modals --}}
    <div class="flex justify-between mb-4">
        <flux:button>
            <flux:modal.trigger name="create-patient">
                Create new
            </flux:modal.trigger>
        </flux:button>
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
    <flux:modal name="create-patient">
        <livewire:patients.create
            modal="create-patient"
        />
    </flux:modal>
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
