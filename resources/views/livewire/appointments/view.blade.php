<?php

use App\Enums\AppointmentStatus;
use App\Livewire\Traits\AppointmentStatusColor;
use App\Models\Appointment;
use Livewire\Volt\Component;

new class extends Component {

    use AppointmentStatusColor;

    public Appointment $appointment;


    public function mount(Appointment $appointment) : void
    {
        $this->appointment = $appointment;
    }
}; ?>

<div>
    <div class="flex flex-row justify-between gap-4 mb-4 text-sm">
        <div class="w-full">
            <h1 class="font-bold">{{ $appointment->title }}</h1>
            <p class="text-sm">{{ $appointment->type }} Appointment</p>
            <livewire:users.show-list :users="$appointment->users" />
        </div>
        <div class="flex-none text-right">
            <p class="font-bold">{{ $appointment->date }}</p>
            <p>{{ $appointment->start_at }} to {{ $appointment->end_at }}</p>
            <p>
                <flux:badge
                        class="mt-1"
                        size="sm"
                        variant="primary"
                        :color="$this->getStatusColor($appointment->status)"
                >{{ $appointment->status }}</flux:badge>
            </p>
        </div>
    </div>
</div>