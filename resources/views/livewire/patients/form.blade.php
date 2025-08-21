<?php

use App\Enums\PatientStatus;
use App\Enums\UserRole;
use App\Livewire\Traits\HasAvatarUpload;
use App\Models\Patient;
use Illuminate\Validation\Rule;
use Livewire\Attributes\On;
use Livewire\Volt\Component;
use Flux\Flux;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileDoesNotExist;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileIsTooBig;
use Illuminate\Support\Facades\DB;

new class extends Component {

    use HasAvatarUpload;

    public ?Patient $patient;
    public string   $status                = "";
    public string   $prefix                = "";
    public string   $first_name            = "";
    public string   $last_name             = "";
    public string   $suffix                = "";
    public string   $email                 = "";
    public string   $date_of_birth         = "";
    public string   $password              = "";
    public string   $password_confirmation = "";

    public string $modal = "";

    public function mount(?Patient $patient) : void
    {
        $this->patient = $patient;
        if ($this->patient?->id) {
            $this->loadPatient($this->patient->id);

        }
    }

    #[On("edit-patient")]
    public function loadPatient($id) : void
    {
        $this->patient = Patient::findOrFail($id);
        $this->fill($this->patient);
        $this->avatar_url = $this->patient->avatar;
        $this->password = "";
    }

    public function save() : void
    {
        $validated = $this->validate();

        $patient_data = [
            'prefix'        => $this->prefix,
            'first_name'    => $this->first_name,
            'last_name'     => $this->last_name,
            'suffix'        => $this->suffix,
            'email'         => $this->email,
            'date_of_birth' => $this->date_of_birth,
            'status'        => $this->status
        ];

        if (!empty($validated['password'] ?? null)) {
            $patient_data['password'] = bcrypt($validated['password']);
        }

        if ($this->patient?->id) {

            $this->patient->update($patient_data);
            $message = "Successfully updated patient";
            $heading = "User updated";
        } else {

            $this->patient = Patient::create($patient_data);
            $message = "Successfully created patient";
            $heading = "User created";
        }

        if ($this->avatar_path !== "") {
            try {
                $this->patient->addMedia(storage_path('app/public/'.$this->avatar_path))
                              ->preservingOriginal()
                              ->toMediaCollection('avatars');
            } catch (FileDoesNotExist|FileIsTooBig $e) {
                dd($e->getMessage());
                Flux::toast("Error saving avatar", heading: "Error", variant: "error", position: "top-right");

            }
        }

        // toast it up
        Flux::toast($message, heading: $heading, variant: "success", position: "top-right");
        $this->cancel();
        $this->dispatch("patients.index:refresh");
    }

    public function cancel() : void
    {
        Flux::modal($this->modal)
            ->close();
    }

    public function rules() : array
    {
        return [
            'prefix'                => 'nullable|max:25',
            'first_name'            => 'required|string|max:255',
            'last_name'             => 'required|string|max:255',
            'suffix'                => 'nullable|max:25',
            'date_of_birth'         => 'required|date',
            'status'                => [
                'required',
                Rule::in(PatientStatus::cases())
            ],
            'email'                 => 'required|email',
            'password'              => $this->patient->id ? 'nullable' : 'required'.'|string|min:1|max:255|confirmed',
            'password_confirmation' => $this->patient->id ? 'nullable' : 'required'.'|string|min:1|max:255'
        ];
    }

    public function with() : array
    {
        return [
            'patient' => $this->patient
        ];
    }

}; ?>

<div>
    <div class="flex gap-4 py-4">
        <div class="w-1/6">
            <flux:input
                    label="Prefix"
                    placeholder="Mr."
                    wire:model="prefix"
            />
        </div>
        <div class="w-auto">
            <flux:input
                    label="First Name"
                    placeholder="John"
                    wire:model="first_name"
            />
        </div>
        <div class="w-auto">
            <flux:input
                    label="Last Name"
                    placeholder="Doe"
                    wire:model="last_name"
            />
        </div>
        <div class="w-1/6">
            <flux:input
                    label="Suffix"
                    placeholder="Jr."
                    wire:model="suffix"
            />
        </div>

    </div>
    <div class="flex gap-4 py-4">
        <div class="w-1/3">
            <flux:input
                    label="Email"
                    placeholder="john.doe@example.com"
                    wire:model="email"
            />
        </div>
        <div class="w-1/3">
            <flux:date-picker
                    wire:model="date_of_birth"
                    label="Date of Birth"
            >
                <x-slot name="trigger">
                    <flux:date-picker.input />
                </x-slot>
            </flux:date-picker>
        </div>
        <div class="w-1/3">
            <flux:select
                    label="Status"
                    variant="listbox"
                    placeholder="Choose Status"
                    wire:model="status"
            >
                @foreach(PatientStatus::cases() as $patient_status)
                    <flux:select.option>{{ $patient_status }}</flux:select.option>
                @endforeach
            </flux:select>
        </div>

    </div>

    <div class="flex gap-4 py-4">
        <div class="w-1/2">
            <flux:input
                    type="password"
                    label="Password"
                    placeholder="Password"
                    wire:model="password"
                    value=""
            />
        </div>
        <div class="w-1/2">
            <flux:input
                    type="password"
                    label="Confirm Password"
                    placeholder="Confirm Password"
                    wire:model="password_confirmation"
                    value=""
            />
        </div>
    </div>
    <flux:callout.text class="text-xs text-center">Only fill out these fields if you are setting or changing the
                                                   password.
    </flux:callout.text>

    <div
            id="avatar"
    >
        <div class="mt-4">

            @if($avatar_url)
                <flux:label for="avatar">Avatar</flux:label>

                <flux:avatar
                        class="flex-none w-24 h-24  rounded-full object-cover mr-4"
                        src="{{ $avatar_url }}"
                        alt="{{ $patient->full_name_extended }}"
                        title="{{ $patient->full_name_extended }}"
                        size="md"
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


    <div class="flex items-center justify-center gap-4 py-4">
        <flux:button
                variant="primary"
                color="lime"
                wire:click="save"
        >Save!
        </flux:button>
        <flux:button wire:click="cancel">Cancel</flux:button>
    </div>
</div>
