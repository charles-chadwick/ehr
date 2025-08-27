<?php


use App\Livewire\Traits\AppointmentStatusColor;
use App\Livewire\Traits\Sortable;
use App\Models\Appointment;
use App\Models\Patient;
use App\Enums\AppointmentStatus;
use Illuminate\Pagination\LengthAwarePaginator;
use LaravelIdea\Helper\App\Models\_IH_Base_C;
use LaravelIdea\Helper\App\Models\_IH_Appointment_C;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Volt\Component;
use Carbon\Carbon;
use Livewire\WithPagination;

new class extends Component {

    use Sortable, AppointmentStatusColor;

    public Patient $patient;

    public function mount(Patient $patient) : void
    {
        $this->patient = $patient;
        $this->sort_by = 'date_and_time';
    }

    #[Computed]
    #[On('appointments.index:refresh')]
    public function appointments() : array|LengthAwarePaginator|_IH_Base_C|_IH_Appointment_C
    {
        return Appointment::where('patient_id', $this->patient->id)
                          ->orderBy($this->sort_by, $this->sort_direction)
                          ->paginate(5);
    }

    public function with() : array
    {
        return [
            'appointments' => $this->appointments
        ];
    }
}; ?>

<div>

    <flux:modal name="appointment-update">
        <livewire:appointments.update modal="appointment-update" :patient="$patient" />
    </flux:modal>

    {{-- table --}}
    <flux:table :paginate="$this->appointments">
        <flux:table.columns>
            <flux:table.column
                    sortable
                    :sorted="$sort_by === 'date_and_time'"
                    :direction="$sort_direction"
                    wire:click="sort('date_and_time')"
            >{{ __('appointments.date_and_time') }}
            </flux:table.column>
            <flux:table.column
                    sortable
                    :sorted="$sort_by === 'title'"
                    :direction="$sort_direction"
                    wire:click="sort('title')"
            >{{ __('appointments.title') }}
            </flux:table.column>
            <flux:table.column
                    sortable
                    :sorted="$sort_by === 'type'"
                    :direction="$sort_direction"
                    wire:click="sort('type')"
            >{{ __('appointments.type') }}
            </flux:table.column>
            <flux:table.column
                    sortable
                    :sorted="$sort_by === 'status'"
                    :direction="$sort_direction"
                    wire:click="sort('status')"
            >{{ __('appointments.status') }}
            </flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @forelse ($appointments as $appointment)
                <flux:table.row :key="$appointment->id">
                    <flux:table.cell>
                        {{ $appointment->date_and_time_range }}
                    </flux:table.cell>
                    <flux:table.cell>
                        <a
                                href="#"
                                class="link"
                        >
                            <flux:modal.trigger
                                    name="appointment-update"
                                    wire:click="$dispatch('appointments.update:load', {appointment: {{ $appointment }}})"
                            >{{ $appointment->title }}
                            </flux:modal.trigger>
                        </a>
                    </flux:table.cell>
                    <flux:table.cell>
                        {{ $appointment->type }}
                    </flux:table.cell>
                    <flux:table.cell>
                        <flux:badge
                                size="sm"
                                variant="primary"
                                color="{{ $this->getStatusColor($appointment->status) }}"
                        >
                            {{ $appointment->status }}
                        </flux:badge>
                    </flux:table.cell>
                </flux:table.row>
            @empty
                <flux:table.row>
                    <flux:table.cell
                            colspan="5"
                            class="text-center"
                    >
                        {{ __('appointments.no_appointments') }}
                    </flux:table.cell>
                </flux:table.row>
            @endforelse
        </flux:table.rows>

    </flux:table>
</div>
