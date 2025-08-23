<?php

use App\Enums\EncounterStatus;
use App\Models\Encounter;
use App\Models\Patient;
use Carbon\Carbon;
use Livewire\Volt\Component;

new class extends Component {

    public string    $type            = "";
    public string    $date_of_service = "";
    public string    $title           = "";
    public string    $content         = "";
    public string    $status          = "";
    public Patient   $patient;
    public Encounter $encounter;

    public function mount(Patient $patient, Encounter $encounter) : void
    {
        $this->patient = $patient;
        $this->encounter = $encounter;
        $this->fill($encounter);
        $encounter->load('signedBy');
    }

    public function unsign() : void
    {
        // update the status
        $this->encounter->update(['status' => EncounterStatus::Unsigned]);

        // log who unsigned it
        activity()
            ->on($this->encounter)
            ->useLog('Database')
            ->event('unsigned')
            ->withProperties([
                'unsigned_by' => auth()->user()->id,
                'unsigned_at' => Carbon::now()
            ])
            ->log("Unsigned Note");

        $this->redirect(route('encounters.form', [
            'patient'   => $this->patient,
            'encounter' => $this->encounter
        ]));
    }
}; ?>

<div>
    <flux:card>
        <livewire:patients.details :patient="$patient" />
    </flux:card>
    <flux:card class="mt-4">
        <div class="flex items-between">
            <h3 class="font-bold text-zinc-800 flex-auto">{{ $title }}</h3>
            <p class="flex-none">
                <flux:button wire:click="unsign">
                    Unsign Note
                </flux:button>
            </p>
        </div>
        <flux:description>
            <p class="text-sm">{{ $type }}</p>
            <p class="text-sm">{{ Carbon::parse($date_of_service)->format('m/d/Y') }}</p>
        </flux:description>
        <hr class="my-4 text-zinc-300" />
        <div
                class="prose prose-sm prose-zinc max-w-none text-zinc-800 text-sm"
                id="encounter"
        >
            {!! $content  !!}
        </div>
        <hr class="my-4 text-zinc-300" />
        <div class="flex items-center justify-between">
            <p class="text-sm text-zinc-500">
                Signed by {{ $encounter->signedBy->full_name_extended }} on {{ $encounter->created_at->format('m/d/Y @ h:ia') }}
            </p>
        </div>
    </flux:card>
</div>
