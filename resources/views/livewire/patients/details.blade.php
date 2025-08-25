<?php

use App\Models\Patient;
use Livewire\Volt\Component;
use Carbon\Carbon;

new class extends Component {

    public Patient $patient;
    public string  $size = "sm";

    public function mount(Patient $patient) : void
    {
        $this->patient = $patient;
    }

    public function with() : array
    {
        return [
            'patient' => $this->patient,
            'size'    => $this->size
        ];
    }
}; ?>

{{-- patient info --}}
<div class="flex">

    {{-- avatar --}}
    <div class="flex-none">
        <flux:avatar
                class="rounded-full object-cover mx-auto w-[102px] h-[102px] mr-4"
                src="{{ $patient->avatar }}"
                alt="{{ $patient->full_name_extended }}"
                title="{{ $patient->full_name_extended }}"
        />
    </div>

    <flux:description class="flex-grow">
        <a href="{{ route('patients.chart', $patient) }}">
            <h1 class="font-bold text-zinc-800">{{ $patient->full_name_extended }} (#{{ $patient->id }})</h1>
        </a>
        @if ($patient->nickname !== "")
            <p class="italic text-sm">"{{ $patient->nickname }}"</p>
        @endif
        <p class="text-sm">{{ $patient->gender }}</p>
        <p class="text-sm">{{ Carbon::parse($patient->date_of_birth)->format('m/d/Y') }}
            ({{ $patient->age }})</p>
        <p class="text-sm">{{ $patient->email }}</p>
    </flux:description>
</div>