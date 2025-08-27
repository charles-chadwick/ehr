<?php


use App\Livewire\Traits\Sortable;
use App\Models\Patient;
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

    public function mount(Patient $patient) : void
    {
        $this->patient = $patient;
        $this->sort_by = 'id';
    }

    #[Computed]
    #[On('patients.index:refresh')]
    public function patients() : array|_IH_Patient_C|LengthAwarePaginator|_IH_Base_C
    {
        return Patient::orderBy($this->sort_by, $this->sort_direction)
                      ->paginate();
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
    <flux:button>
        <flux:modal.trigger name="create-patient">
            Create new
        </flux:modal.trigger>
    </flux:button>
    <flux:modal name="create-patient">
        <livewire:patients.create
                modal="create-patient"
        />
    </flux:modal>

    {{-- patient list --}}
    <ul
            role="list"
            class="divide-y divide-gray-100 dark:divide-white/5  text-sm"
    >
        @forelse($patients as $patient)
            <li
                    wire:key="patient-{{ $patient->id }}"
                    class="flex flex-wrap items-center justify-between gap-x-6 gap-y-4 py-5 sm:flex-nowrap"
            >
                <div>
                    <p class="font-semibold">
                        <a
                                href="#"
                                class="link font-bold"
                        >
                            <a
                                    class="link"
                                    href="{{ route('patients.chart', $patient) }}"
                            >{{ $patient->full_name }}</a>
                        </a>
                    </p>
                    <div class="mt-1 flex items-center gap-x-2">
                        <p>
                            {{ $patient->dob }}
                        </p>
                    </div>
                </div>

            </li>
        @empty
            <li class="text-center">{{ __('ehr.no_records', ['items' => __('patients.patients')]) }}</li>
        @endforelse
    </ul>
</div>
