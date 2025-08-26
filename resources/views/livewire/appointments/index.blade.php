<?php

use App\Livewire\Traits\AppointmentStatusColor;
use App\Models\Appointment;
use App\Models\Patient;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;
    use AppointmentStatusColor;

    public Patient $patient;

    public function mount(Patient $patient) : void
    {
        $this->appointments = $this->appointments();
    }

    #[Computed]
    #[On('appointments.index:refresh')]
    public function appointments() : array|LengthAwarePaginator
    {
        return Appointment::with('users')
                          ->where('patient_id', $this->patient->id)
                          ->paginate();
    }

    public function with() : array
    {
        return ['appointments' => $this->appointments];
    }
}; ?>

<div>
    <flux:table :paginate="$this->appointments">

        <flux:table.columns>
            <flux:table.column>Date and Time</flux:table.column>
            <flux:table.column>Type</flux:table.column>
            <flux:table.column>Title</flux:table.column>
            <flux:table.column>With</flux:table.column>
            <flux:table.column>Status</flux:table.column>
        </flux:table.columns>
        <flux:table.rows>
            @forelse($appointments as $appointment)
                <flux:table.row :key="$appointment->id">
                    <flux:table.cell>
                        {{ $appointment->date_and_time_range }}
                    </flux:table.cell>

                    <flux:table.cell>
                        {{ $appointment->type }}
                    </flux:table.cell>
                    <flux:table.cell>
                        {{ $appointment->title }}
                    </flux:table.cell>
                    <flux:table.cell>
                        <livewire:users.show-list :users="$appointment->users" />
                    </flux:table.cell>
                    <flux:table.cell>
                        <flux:badge
                                class="mt-1"
                                size="sm"
                                variant="primary"
                                :color="$this->getStatusColor($appointment->status)"
                        >{{ $appointment->status }}</flux:badge>
                    </flux:table.cell>
                </flux:table.row>
            @empty
                <flux:table.row>
                    <flux:table.cell colspan="5">
                        No appointments found.
                    </flux:table.cell>
                </flux:table.row>
            @endforelse
        </flux:table.rows>
    </flux:table>
</div>