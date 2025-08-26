<?php

use App\Livewire\Traits\PatientStatusColor;
use App\Models\Patient;
use Livewire\Volt\Component;

new class extends Component {
    use PatientStatusColor;

    public Patient $patient;
    public string  $menu = "";
}; ?>

<div>
    <div class="flex flex-row text-sm">
        <div class="flex-none mr-2">
            <flux:avatar
                    src="{{ $patient->avatar }}"
                    alt=""
                    class="w-16 h-16"
            />
        </div>
        <div class="flex-grow">
            <a
                    href="{{ route('patients.chart', $patient) }}"
                    class="font-semibold"
            >
                {{ $patient->full_name_extended }}
            </a>
            <p>{{ $patient->gender }} / {{ $patient->age }} ({{ $patient->dob }})</p>
            <p>
                <flux:badge
                        class="h-5"
                        size="sm"
                        variant="primary"
                        color="{{ $this->statusColor($patient->status) }}"
                >
                    {{ $patient->status }}
                </flux:badge>
            </p>
        </div>
        <div class="flex-none text-right">
            @if($menu !== "")
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
                            {{ __('ehr.edit_profile') }}
                        </flux:navmenu.item>
                    </flux:navmenu>
                </flux:dropdown>
            @endif
        </div>
    </div>
</div>
