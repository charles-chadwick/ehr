<?php

use App\Enums\PatientGender;
use App\Enums\PatientStatus;
use App\Enums\UserRole;
use App\Livewire\Traits\HasAvatarUpload;
use App\Models\Patient;
use App\Models\Traits\UsesModal;
use Flux\Flux;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Livewire\Attributes\On;
use Livewire\Volt\Component;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileDoesNotExist;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileIsTooBig;

new class extends Component {

    use HasAvatarUpload, UsesModal;

    public ?Patient $patient;
    public string   $status                = "";
    public string   $prefix                = "";
    public string   $first_name            = "";
    public string   $middle_name           = "";
    public string   $last_name             = "";
    public string   $suffix                = "";
    public string   $nickname              = "";
    public string   $email                 = "";
    public string   $date_of_birth         = "";
    public string   $gender                = "";
    public string   $gender_identity       = "";
    public string   $password              = "";
    public string   $password_confirmation = "";


    public function mount(?Patient $patient) : void
    {
        $this->patient = $patient ?? new Patient();
        if ($this->patient->exists) {
            $this->loadPatient($this->patient->id);
        }
    }

    #[On("patient.form:edit")]
    public function loadPatient(int $id = 0) : void
    {
        if ($id > 0) {
            $this->patient = Patient::findOrFail($id);
            $this->fill($this->patient);
            $this->avatar_url = $this->patient->avatar;
            $this->password = ""; // IDK what this all aboot but it's needed 
        }
    }

    public function save() : void
    {
        $validated = $this->validate();

        $patient_data = [
            'status'          => $this->status,
            'prefix'          => $this->prefix,
            'first_name'      => $this->first_name,
            'middle_name'     => $this->middle_name,
            'last_name'       => $this->last_name,
            'suffix'          => $this->suffix,
            'nickname'        => $this->nickname,
            'email'           => $this->email,
            'date_of_birth'   => $this->date_of_birth,
            'gender'          => $this->gender,
            'gender_identity' => $this->gender_identity,
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
        $this->closeModal(['patient']);
        $this->dispatch("patients.index:refresh");
    }

    public function rules() : array
    {
        return [
            'prefix'                => 'nullable|max:25',
            'first_name'            => 'required|string|max:255',
            'middle_name'           => 'nullable|string|max:255',
            'last_name'             => 'required|string|max:255',
            'suffix'                => 'nullable|max:25',
            'nickname'              => 'nullable|max:255',
            'gender'                => [
                'required',
                Rule::in(PatientGender::cases())
            ],
            'gender_identity'       => 'nullable|max:255',
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

<flux:modal
        variant="flyout"
        class="w-1/2"
        name="{{ $modal }}"
        wire:cancel="closeModal(['patient'])"
>
    <div class="flex gap-4 py-4">
        <div class="w-auto">
            <flux:input
                    label="First Name"
                    placeholder="John"
                    wire:model="first_name"
            />
        </div>
        <div class="w-auto">
            <flux:input
                    label="Middle Name"
                    placeholder="Middle Name"
                    wire:model="middle_name"
            />
        </div>
        <div class="w-auto">
            <flux:input
                    label="Last Name"
                    placeholder="Doe"
                    wire:model="last_name"
            />
        </div>
    </div>

    <div class="flex gap-4 py-4">
        <div class="w-auto">
            <flux:input
                    label="Prefix"
                    placeholder="Mr., Mrs., Ms., etc."
                    wire:model="prefix"
            />
        </div>
        <div class="w-auto">
            <flux:input
                    label="Suffix"
                    placeholder="Jr, Sr, III, etc."
                    wire:model="suffix"
            />
        </div>
        <div class="w-auto">
            <flux:input
                    label="Nickname"
                    placeholder="Bubba, Curly, etc."
                    wire:model="nickname"
            />
        </div>
    </div>

    {{-- date of birth / gender / gender identity --}}
    <div class="flex gap-4 py-4">
        <div class="w-1/3">
            <flux:date-picker
                    selectable-header
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
                    label="Gender"
                    variant="listbox"
                    placeholder="Choose Gender"
                    wire:model="gender"
            >
                @foreach(PatientGender::cases() as $gender)
                    <flux:select.option>{{ $gender }}</flux:select.option>
                @endforeach
            </flux:select>
        </div>
        <div class="w-1/3">
            <flux:input
                    label="Gender Identity"
                    placeholder="Male, Female, Other, etc."
                    wire:model="gender_identity"
            />
        </div>

    </div>

    {{-- email, status, avatar --}}
    <div class="flex gap-4 py-4">

        {{-- status --}}
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

        {{-- email--}}
        <div class="w-1/3">
            <flux:input
                    label="Email"
                    placeholder="john.doe@example.com"
                    wire:model="email"
            />
        </div>
    </div>

    {{-- password stuff --}}
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
                color="emerald"
                wire:click="save"
        >Save!
        </flux:button>
        <flux:button wire:click="closeModal(['patient'])">Cancel</flux:button>
    </div>
</flux:modal>
