<?php
namespace App\Livewire\Traits;

use App\Enums\AppointmentStatus;

trait AppointmentStatusColor
{
    private function getStatusColor(AppointmentStatus $status) : string
    {
        return match ($status) {
            AppointmentStatus::Confirmed                            => 'emerald',
            AppointmentStatus::Cancelled, AppointmentStatus::NoShow => 'red',
            AppointmentStatus::Rescheduled                          => 'pink',
            AppointmentStatus::Pending                              => 'yellow',
            default                                                 => 'gray',
        };
    }
}