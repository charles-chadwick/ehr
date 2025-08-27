<?php

use App\Enums\EncounterStatus;
use App\Enums\EncounterType;
use App\Livewire\Forms\EncounterForm;
use App\Models\Patient;
use Carbon\Carbon;
use Flux\Flux;
use Livewire\Volt\Component;

new class extends Component {

    public EncounterForm $form;
    public Patient       $patient;
    public               $date_of_service;
    public               $type;
    public               $status;
    public               $title;
    public               $content;

    public function mount(Patient $patient) : void
    {
        $this->form->patient = $patient;
        $this->form->date_of_service = Carbon::tomorrow()
                                       ->format('Y-m-d');
    }

    public function saveWithoutSigning() : void
    {
        $this->save(EncounterStatus::Unsigned);
    }

    public function saveAndSign() : void
    {
        $this->save(EncounterStatus::Signed);
    }

    private function save(EncounterStatus $status) : void
    {
        // try and save the encounter
        $encounter = $this->form->save($status);

        // check for results
        if ($encounter->exists) {

            // set the success messages
            $message = "Successfully created encounter.";
            $heading = "Success";
            $variant = "success";

            // reset the thing
            $this->form->resetExcept('patient');

            // toast and then redirect if need be
            Flux::toast($message, heading: $heading, variant: $variant);

            if ($status == EncounterStatus::Signed) {
                $this->redirect(route('encounters.view', $encounter));
            }
        } else {

            // set the error messages
            $message = "Error creating encounter. Please contact administrator.";
            $heading = "Error";
            $variant = "danger";
            Flux::toast($message, heading: $heading, variant: $variant);
        }


    }
}; ?>


<form
        wire:submit="saveWithoutSigning"
>
    {{-- Title, Type and DOS --}}
    <div class="flex flex-row gap-4">
        <div class="flex-1/2">
            <flux:input
                    label="{{ __('encounters.title') }}"
                    placeholder="{{ __('encounters.my_encounter') }}"
                    wire:model="form.title"
            />
        </div>
        <div class="flex-1/4">
            <flux:select
                    label="{{ __('encounters.type') }}"
                    placeholder="{{ __('Choose Type') }}"
                    variant="listbox"
                    wire:model="form.type"
            >
                @foreach(EncounterType::cases() as $encounter_type)
                    <flux:select.option>{{ $encounter_type }}</flux:select.option>
                @endforeach
            </flux:select>
        </div>
        <div class="flex-1/4">
            <flux:date-picker
                    label="{{ __('encounters.date_of_service') }}"
                    selectable-header
                    value="{{ $date_of_service }}"
                    wire:model="form.date_of_service"
            >
                <x-slot name="trigger">
                    <flux:date-picker.input />
                </x-slot>
            </flux:date-picker>
        </div>
    </div>

    {{-- content --}}
    <div class="gap-4 mt-4">
        <flux:editor
                class="h-full min-h-[600px]"
                wire:model="form.content"
        />
    </div>

    {{-- buttons --}}
    <div class="px-2 mt-4 text-center">
        <flux:button
                color="emerald"
                type="submit"
                variant="primary"
        >
            {{ __('ehr.save') }}
        </flux:button>
        <flux:button
                color="primary"
                wire:click="saveAndSign"
        >
            {{ __('encounters.save_and_sign') }}
        </flux:button>
    </div>

</form>

