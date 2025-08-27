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
        return Appointment::with('users')
                          ->where('patient_id', $this->patient->id)
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
        <livewire:appointments.update
                modal="appointment-update"
                :patient="$patient"
        />
    </flux:modal>
    <ul
            role="list"
            class="divide-y divide-gray-100 dark:divide-white/5  text-sm"
    >
        @forelse($appointments as $appointment)
            <li
                    wire:key="appointment-{{ $appointment->id }}"
                    class="flex flex-wrap items-center justify-between gap-x-6 gap-y-4 py-5 sm:flex-nowrap"
            >
                <div>
                    <p class="font-semibold">
                        <a
                                href="#"
                                class="link font-bold"
                        >
                            <flux:modal.trigger
                                    name="appointment-update"
                                    wire:click="$dispatch('appointments.update:load', {appointment: {{ $appointment }}})"
                            >{{ $appointment->title }}</flux:modal.trigger>
                        </a>
                    </p>
                    <div class="mt-1 flex items-center gap-x-2">
                        <p>
                            {{ $appointment->date_and_time_range }}
                        </p>
                    </div>
                </div>
                {{-- users --}}
                <livewire:users.show-list
                        wire:key="show-list-{{ uniqid() }}"
                        :users="$appointment->users"
                />
            </li>
        @empty
            <li class="text-center">{{ __('ehr.no_records', ['items' => __('appointments.appointments')]) }}</li>
        @endforelse
    </ul>
</div>
