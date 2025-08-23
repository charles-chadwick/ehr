<?php

use App\Enums\EncounterStatus;
use App\Enums\EncounterType;
use App\Models\Encounter;
use App\Models\Patient;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\Rule;
use JetBrains\PhpStorm\NoReturn;
use Livewire\Attributes\On;
use Livewire\Volt\Component;
use Flux\Flux;

new class extends Component {

    public string    $type            = "";
    public string    $date_of_service = "";
    public string    $title           = "";
    public string    $content         = "";
    public string    $status          = "";
    public Patient   $patient;
    public Encounter $encounter;

    public function mount(?Encounter $encounter) : void
    {
        $this->loadEncounter($encounter);
        $this->patient = request()->patient;
    }

    #[On("encounter-form:refresh")]
    public function loadEncounter(Encounter $encounter) : void
    {
        if (isset($encounter->id) && $encounter->id !== null) {
            $this->encounter = $encounter;
            $this->fill($this->encounter);
        } else {

            $this->date_of_service = Carbon::now()
                                           ->format('Y-m-d');
        }
    }

    public function rules() : array
    {
        return [
            'title'           => 'required|max:255',
            'date_of_service' => 'date',
            'type'            => [
                'required',
                Rule::in(EncounterType::cases())
            ],
            'content'         => 'nullable'
        ];
    }

    public function saveWithoutSigning() : void
    {
        $this->save();

    }

    public function saveAndSign() : void
    {
        $this->save(true);

    }

    private function save($sign = false) : void
    {
        $this->validate();

        $data = [
            'content'         => $this->content,
            'date_of_service' => $this->date_of_service,
            'title'           => $this->title,
            'type'            => $this->type,
            'status'          => $sign ? EncounterStatus::Signed : EncounterStatus::Unsigned,
            'singed_by'       => $sign ? auth()->user() : null,
            'signed_at'       => $sign ? Carbon::now() : null,
            'patient_id'      => $this->patient->id
        ];


        if (isset($this->encounter->id) && $this->encounter->id !== null) {

            // updating
            $this->encounter->update($data);
            // toast it up
            Flux::toast("Successfully saved encounter", heading : "Encounter saved", variant: "success",
                                                        position: "top-right");
        } else {

            // saving
            $this->encounter = Encounter::create($data);
            // toast it up
            Flux::toast("Successfully saved encounter", heading : "Encounter saved", variant: "success",
                                                        position: "top-right");
         }

        if ($sign) {
            $this->redirect(route('encounters.view', ['patient' => $this->patient, 'encounter' => $this->encounter]));
        }

        // $encounter = $this->encounter;
    }

}; ?>

<div>
    <flux:card class="mt-4">
        <livewire:patients.details :patient="$patient" />
    </flux:card>
    <flux:card class="mt-4">
        <form
                wire:submit="saveWithoutSigning"
        >
            <div class="flex flex-row gap-4">
                <div class="flex-1/2">
                    <flux:input
                            label="Title"
                            wire:model="title"
                            placeholder="My Encounter"
                    />
                </div>
                <div class="flex-1/4">
                    <flux:select
                            label="Type"
                            variant="listbox"
                            placeholder="Choose Type"
                            wire:model="type"
                    >
                        @foreach(EncounterType::cases() as $encounter_type)
                            <flux:select.option>{{ $encounter_type }}</flux:select.option>
                        @endforeach
                    </flux:select>
                </div>
                <div class="flex-1/4">
                    <flux:date-picker
                            wire:model="date_of_service"
                            label="Date of Service"
                            value="{{ $date_of_service }}"
                    >
                        <x-slot name="trigger">
                            <flux:date-picker.input />
                        </x-slot>
                    </flux:date-picker>
                </div>
            </div>
            <div class="gap-4 mt-4">
                <flux:editor
                        class="h-full min-h-[600px]"
                        wire:model="content"
                />
            </div>
            <div class="px-2 mt-4 text-center">
                <flux:button
                        type="submit"
                        color="primary"
                >
                    Save Encounter
                </flux:button>
                <flux:button
                        color="primary"
                        wire:click="saveAndSign"
                >
                    Save and Sign Encounter
                </flux:button>
            </div>

        </form>
    </flux:card>
</div>
