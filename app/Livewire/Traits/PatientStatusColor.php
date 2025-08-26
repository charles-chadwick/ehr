<?php

namespace App\Livewire\Traits;

use App\Enums\PatientStatus;

trait PatientStatusColor
{
    private function statusColor(PatientStatus $status) : string
    {
        return match ($status) {
            PatientStatus::Active      => 'emerald',
            PatientStatus::Inactive    => 'gray',
            PatientStatus::Prospective => 'yellow',
        };
    }
}