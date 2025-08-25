<?php

use App\Enums\PatientStatus;
use App\Models\Patient;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use LaravelIdea\Helper\App\Models\_IH_Base_C;
use LaravelIdea\Helper\App\Models\_IH_Patient_C;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Volt\Component;
use Carbon\Carbon;
use Livewire\WithPagination;

new class extends Component {

    use WithPagination;

    public function mount() : void {}

    public function statusColor($status) : string
    {
        return match ($status) {
            PatientStatus::Prospective->value => 'purple',
            PatientStatus::Inactive->value    => 'gray',
            PatientStatus::Active->value      => 'emerald',
        };
    }

    #[Computed]
    #[On("patients.index:refresh")]
    public function patients() : array|_IH_Patient_C|LengthAwarePaginator|_IH_Base_C
    {
        return Patient::paginate();
    }

    public function with() : array
    {
        return ['patients' => $this->patients];
    }
}; ?>

<div>

    <livewire:patients.form modal="patient-form" />

    <flux:card size="sm">
        <flux:modal.trigger name="patient-form">
            <flux:button
                    variant="primary"
                    color="emerald"
            >{{ __('patients.create_new') }}
            </flux:button>
        </flux:modal.trigger>
        <flux:pagination :paginator="$patients" class="border-t-0 pb-2" />

        @forelse($patients as $patient)
            <div
                    wire:key="{{ $patient->id }}"
                    class="flex justify-between px-2 py-2"
            >
                <div class="w-auto">
                    <div class="flex justify-between px-2 py-2">
                        <flux:avatar
                                class="flex-none w-16 h-16 rounded-full object-cover mr-4"
                                src="{{ $patient->avatar }}"
                                alt="{{ $patient->full_name_extended }}"
                                title="{{ $patient->full_name_extended }}"
                                size="md"
                        />
                        <div class="w-auto text-sm text-zinc-700">
                            <h3 class="font-semibold ">
                                <a href="{{ route('patients.chart', $patient) }}">
                                    {{ $patient->full_name }} {{ $patient->suffix }}
                                </a>
                            </h3>
                            <div class="flex">
                                <span class="font-bold">(#{{ $patient->id }})</span>
                                <flux:badge
                                        size="sm"
                                        class="ml-2"
                                        color="{{ $this->statusColor($patient->status) }}"
                                >{{ $patient->status }}</flux:badge>
                            </div>
                            <p> {{ $patient->gender }} - {{ Carbon::parse($patient->date_of_birth)->format('m/d/Y') }}
                                                       ({{ $patient->age }})</p>
                        </div>
                    </div>
                </div>
                <div class="shrink-0">
                    <flux:dropdown
                            position="bottom"
                            align="end"
                    >
                        <flux:button
                                size="sm"
                                variant="ghost"
                                icon="ellipsis-horizontal"
                        ></flux:button>
                        <flux:navmenu>
                            <flux:navmenu.item href="{{ route('patients.chart', $patient) }}">Go to Chart
                            </flux:navmenu.item>
                        </flux:navmenu>
                    </flux:dropdown>
                </div>
            </div>
        @empty
            <div class="text-center text-zinc-700">
                There are no patients in the system.
            </div>
        @endforelse
            <flux:pagination :paginator="$patients" />
    </flux:card>

</div>
