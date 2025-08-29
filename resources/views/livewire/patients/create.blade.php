<?php

use App\Enums\PatientGender;
use App\Enums\PatientStatus;
use App\Livewire\Forms\PatientForm;
use App\Livewire\Traits\HasAvatarUpload;
use App\Models\Patient;
use Flux\Flux;
use Livewire\Attributes\On;
use Livewire\Volt\Component;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileDoesNotExist;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileIsTooBig;

new class extends Component {

    use HasAvatarUpload;

    public PatientForm $form;
    public Patient     $patient;
    public string      $modal = "";

    public function save() : void
    {
        $patient = $this->form->save();

        if ($patient->exists) {

            $this->saveAvatar($patient);

            // success
            $message = __('ehr.success_created', ['item' => __('patients.patient')]);
            $heading = __('ehr.success_heading');
            $variant = "success";

            $this->dispatch('patients.details:refresh');
            Flux::modal($this->modal)
                ->close();
        } else {
            // error
            $message = __('ehr.error_creating', ['item' => __('patients.patient')]);
            $heading = __('ehr.error_heading');
            $variant = "danger";
        }

        Flux::toast($message, heading: $heading, variant: $variant, position: "top-right");
    }

}; ?>
<form
        wire:submit="save"
        name="patient-form"
        class="min-w-1/3"
        variant="flyout"
>

    {{-- name --}}
    <div class="flex gap-4 py-4">
        <div class="w-auto">
            <flux:input
                    label="{{ __('patients.first_name') }}"
                    placeholder="{{ __('patients.first_name') }}"
                    wire:model="form.first_name"
            />
        </div>
        <div class="w-auto">
            <flux:input
                    label="{{ __('patients.middle_name') }}"
                    placeholder="{{ __('patients.middle_name') }}"
                    wire:model="form.middle_name"
            />
        </div>
        <div class="w-auto">
            <flux:input
                    label="{{ __('patients.last_name') }}"
                    placeholder="{{ __('patients.last_name') }}"
                    wire:model="form.last_name"
            />
        </div>
    </div>

    {{-- salutations --}}
    <div class="flex gap-4 py-4">
        <div class="w-auto">
            <flux:input
                    label="{{ __('patients.prefix') }}"
                    placeholder="{{ __('patients.prefix') }}"
                    wire:model="form.prefix"
            />
        </div>
        <div class="w-auto">
            <flux:input
                    label="{{ __('patients.suffix') }}"
                    placeholder="{{ __('patients.suffix') }}"
                    wire:model="form.suffix"
            />
        </div>
        <div class="w-auto">
            <flux:input
                    label="{{ __('patients.nickname') }}"
                    placeholder="{{ __('patients.nickname') }}"
                    wire:model="form.nickname"
            />
        </div>
    </div>

    {{-- date of birth / gender / gender identity --}}
    <div class="flex gap-4 py-4">
        <div class="w-1/3">
            <flux:date-picker
                    selectable-header
                    wire:model="form.date_of_birth"
                    label="{{ __('patients.date_of_birth') }}"
                    placeholder="{{ __('patients.date_of_birth') }}"
            >
                <x-slot name="trigger">
                    <flux:date-picker.input />
                </x-slot>
            </flux:date-picker>
        </div>
        <div class="w-1/3">
            <flux:select
                    label="{{ __('patients.gender') }}"
                    placeholder="{{ __('patients.gender') }}"
                    variant="listbox"
                    wire:model="form.gender"
            >
                @foreach(PatientGender::cases() as $gender)
                    <flux:select.option>{{ $gender }}</flux:select.option>
                @endforeach
            </flux:select>
        </div>
        <div class="w-1/3">
            <flux:input
                    label="{{ __('patients.gender_identity') }}"
                    placeholder="{{ __('patients.gender_identity') }}"
                    wire:model="form.gender_identity"
            />
        </div>
    </div>

    {{-- email, status --}}
    <div class="flex gap-4 py-4">

        {{-- status --}}
        <div class="w-1/3">
            <flux:select
                    label="{{ __('patients.status') }}"
                    placeholder="{{ __('patients.status') }}"
                    variant="listbox"
                    wire:model="form.status"
            >
                @foreach(PatientStatus::cases() as $patient_status)
                    <flux:select.option>{{ $patient_status->name }}</flux:select.option>
                @endforeach
            </flux:select>
        </div>

        {{-- email--}}
        <div class="w-2/3">
            <flux:input
                    label="{{ __('patients.email') }}"
                    placeholder="{{ __('patients.email') }}"
                    wire:model="form.email"
            />
        </div>
    </div>

    {{-- password stuff --}}
    <div class="flex gap-4 py-4">
        <div class="w-1/2">
            <flux:input
                    type="password"
                    label="{{ __('patients.password') }}"
                    placeholder="{{ __('patients.password') }}"
                    wire:model="form.password"
                    value=""
            />
        </div>
        <div class="w-1/2">
            <flux:input
                    type="password"
                    label="{{ __('patients.confirm_password') }}"
                    placeholder="{{ __('patients.confirm_password') }}"
                    wire:model="form.password_confirmation"
                    value=""
            />
        </div>
    </div>
    <flux:callout.text
            class="text-xs text-center"
    >Only fill out these fields if you are setting or changing the password.
    </flux:callout.text>

    {{-- avatar --}}
    <div
            id="avatar"
    >
        <div class="mt-4">

            @if($avatar_url)
                <flux:label for="avatar">Avatar</flux:label>

                <img
                        class="flex-none w-24 h-24  rounded-full object-cover mr-4"
                        src="{{ $avatar_url }}"
                        size="md"
                        alt="Avatar"
                />
                <a
                        wire:click="removeImage"
                        href="#"
                        class="text-xs text-gray-500 truncate max-w-xs"
                >Remove</a>
            @else
                <livewire:documents.upload
                        directory="avatars"
                        disk="public"
                        event="file-uploaded"
                        accept="image/*"
                        label="Avatar"
                        wire:key="avatar-uploader"
                />
            @endif
        </div>
    </div>

    {{-- submit button --}}
    <div class="px-2 mt-4 text-center">
        <flux:button
                variant="primary"
                color="emerald"
                type="submit"
        >
            {{ __('ehr.save') }}
        </flux:button>
    </div>
</form>