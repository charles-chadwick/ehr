<?php

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

    use Sortable;

    public Patient $patient;

    public function mount(Patient $patient) : void
    {
        $this->patient = $patient;
        $this->sort_by = 'date_and_time';
    }

    #[Computed]
    #[On('appointment.index:refresh')]
    public function appointments() : array|LengthAwarePaginator|_IH_Base_C|_IH_Appointment_C
    {
        return Appointment::where('patient_id', $this->patient->id)
                          ->orderBy($this->sort_by, $this->sort_direction)
                          ->paginate();
    }

    public function with() : array
    {
        return [
            'appointments' => $this->appointments
        ];
    }
}; ?>

<div>
    <div class="flex flex-row">
        <h2 class="font-bold w-full">{{ __('appointments.appointments') }}</h2>
        <div class="flex-none">

            <flux:button
                    size="sm"
                    variant="primary"
                    color="emerald"
            >
                <flux:modal.trigger
                        name="appointment-form"
                        wire:click="$dispatch('appointment.form:edit', {id: 0, patient: {{ $patient }}})"
                >{{ __('appointments.new_appointment') }}
                </flux:modal.trigger>
            </flux:button>
        </div>
    </div>

    <livewire:appointments.form
            modal="appointment-form"
            :patient="$patient"
    />

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
                        {{ $appointment->full_date_and_time }}
                    </flux:table.cell>
                    <flux:table.cell>
                        <a
                                href="#"
                                class="link"
                        >
                            <flux:modal.trigger
                                    name="appointment-form"
                                    wire:click="$dispatch('appointment.form:edit', {id: {{ $appointment->id }}, patient: {{ $patient }}})"
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
                        >
                            {{ $appointment->status }}
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
                                        href="#"
                                        icon="user"
                                >
                                    {{ __('appointments.go_to_appointment') }}
                                </flux:navmenu.item>
                            </flux:navmenu>
                        </flux:dropdown>
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
