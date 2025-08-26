<?php

use App\Models\Patient;
use Livewire\Volt\Component;

new class extends Component {
    public Patient $patient;
}; ?>

<div>
    <livewire:patients.details :patient="$patient" />
</div>
