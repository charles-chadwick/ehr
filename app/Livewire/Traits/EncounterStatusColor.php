<?php

namespace App\Livewire\Traits;

use App\Enums\EncounterStatus;

trait EncounterStatusColor
{
    private function getStatusColor(EncounterStatus $status): string
    {
        return match ($status) {
            EncounterStatus::Signed => 'emerald',
            EncounterStatus::Unsigned => 'gray'
        };
    }
}
