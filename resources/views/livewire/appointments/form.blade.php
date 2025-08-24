<?php

use App\Enums\AppointmentStatus;
use App\Models\Appointment;
use App\Models\AppointmentUser;
use App\Models\Patient;
use App\Models\Traits\UsesModal;
use App\Models\User;
use Carbon\Carbon;
use Flux\Flux;
use Illuminate\Validation\Rule;
use Livewire\Attributes\On;
use Livewire\Volt\Component;

new class extends Component {

    use UsesModal;

    public string      $type              = "";
    public string      $date              = "";
    public string      $time              = "";
    public string      $length            = "";
    public string      $title             = "";
    public string      $description       = "";
    public string      $status            = "";
    public string      $unavailable       = "2025-08-22,2025-08-24";
    public Patient     $patient;
    public Appointment $appointment;
    public array       $selected_user_ids = [];
    public string      $message           = "";

    public function removeUser(int $id) : void
    {
        $this->selected_user_ids = array_values(array_filter($this->selected_user_ids,
            fn(int $userId) => $userId !== $id));
    }

    public function mount(?Appointment $appointment) : void
    {
        // initialize the appointment
        $this->appointment = $appointment ?? new Appointment();

        // if the appointment has actual data, load it
        if ($this->appointment->exists) {
            $this->loadAppointment($this->appointment->id);
        }

        // set the patient from the request
        $this->patient = request()->patient;
    }

    #[On("appointment.form:edit")]
    public function loadAppointment(int $id = 0) : void
    {
        if ($id > 0) {

            // find the appointment and fill the data
            // @TODO: maybe not fail here, as it throws a 404
            $this->appointment = Appointment::findOrFail($id);
            $this->fill($this->appointment);
            $this->setDateAndTime($this->appointment->date_and_time);
            $this->selected_user_ids = $this->appointment->users()
                                                         ->pluck('user_id')
                                                         ->all();
        } else {

            // initialize the date and time
            $this->setDateAndTime(Carbon::today()
                                        ->nextWeekday()
                                        ->setHour(8)
                                        ->setMinute(0));
        }
    }

    public function rules() : array
    {
        return [
            'date'                => 'required|date',
            'time'                => 'required',
            'length'              => 'required|integer',
            'status'              => [
                'required',
                Rule::in(AppointmentStatus::cases()),
            ],
            'title'               => 'required',
            'type'                => 'required',
            'description'         => 'nullable',
            'selected_user_ids'   => 'array',
            'selected_user_ids.*' => 'integer|exists:users,id',
        ];
    }

    public function save() : void
    {
        $validated = $this->validate();
        $start_date_and_time = $this->getDateAndTime($validated['date'], $validated['time']);
        $length = (int) $validated['length'];
        $data = [
            'patient_id'    => $this->patient->id,
            'date_and_time' => $start_date_and_time,
            'length'        => $length,
            'status'        => $this->status,
            'type'          => $this->type,
            'title'         => $this->title,
            'description'   => $this->description,
        ];

        if ($this->appointment->exists) {

            // check for appointment start
            if (!$this->appointment->isAvailable($this->selected_user_ids, $start_date_and_time, $length)) {
                $this->message = "Some of the users have conflicting appointments";
                return;
            }

            $this->appointment->update($data);

        } else {
            $this->appointment = Appointment::create($data);
        }

        // user stuff
        $appointment_users = new AppointmentUser();
        $appointment_users->syncUsers($this->appointment->id, $this->selected_user_ids);

        // notify and dispatch
        Flux::toast("Successfully saved appointment", heading : "Appointment saved", variant: "success",
                                                      position: "top-right");
        $this->dispatch('appointment.index:refresh');
        $this->closeModal([
            'patient',
            'appointment'
        ]);
    }


    private function setDateAndTime(Carbon $date_and_time) : void
    {
        $this->date = $date_and_time->format('Y-m-d');
        $this->time = $date_and_time->format('H:i');
    }

    private function getDateAndTime(string $date, string $time) : Carbon
    {
        list($hour, $minute) = explode(':', $time);

        return Carbon::parse($date)
                     ->addHours((int) $hour)
                     ->addMinutes((int) $minute);
    }
}; ?>


<flux:modal
        name="appointment-form"
        class="min-w-1/3"
        variant="flyout"
        wire:close="closeModal(['patient', 'appointment'])"
>
    @if($message != "")
    <div class="bg-red-500 text-white p-4 rounded-md">
    {{ $message }}
    </div>
    @endif

    <form wire:submit.prevent="save">
        {{-- title, type, and status --}}
        <div class="flex flex-row gap-4">
            <div class="flex-1/2">
                <flux:input
                        label="{{ __('appointments.title') }}"
                        placeholder="{{ __('appointments.title') }}"
                        wire:model="title"
                />
            </div>
            <div class="flex-1/4">
                <flux:input
                        label="{{ __('appointments.type') }}"
                        placeholder="{{ __('appointments.type') }}"
                        wire:model="type"
                />
            </div>
            <div class="flex-1/4">
                <flux:select
                        label="{{ __('appointments.status') }}"
                        placeholder="{{ __('appointments.choose_status') }}"
                        variant="listbox"
                        wire:model="status"
                >
                    @foreach(AppointmentStatus::cases() as $appointment_status)
                        <flux:select.option>{{ $appointment_status->value }}</flux:select.option>
                    @endforeach
                </flux:select>
            </div>
        </div>

        {{-- date and time --}}
        <div class="flex flex-row gap-4 mt-4">
            <div class="flex-1/2">
                <flux:date-picker
                        selectable-header
                        unavailable="{{ $unavailable }}"
                        wire:model="date"
                        label="{{ __('appointments.date') }}"
                >
                    <x-slot name="trigger">
                        <flux:date-picker.input />
                    </x-slot>
                </flux:date-picker>
            </div>
            <div class="flex-1/4">
                <flux:input
                        type="time"
                        wire:model="time"
                        label="{{ __('appointments.time') }}"
                />
            </div>
            <div class="flex-1/4">
                <flux:input
                        type="length"
                        wire:model="length"
                        label="{{ __('appointments.length') }}"
                        placeholder="{{ __('appointments.in_minutes') }}"
                />
            </div>
        </div>

        {{-- user selection --}}
        <div class="mt-4">
            <flux:select
                    variant="listbox"
                    searchable
                    placeholder="{{ __('users.choose_users') }}"
                    wire:model.live="selected_user_ids"
                    label="{{ __('users.users') }}"
                    multiple
            >
                @foreach(User::all() as $user)
                    <flux:select.option value="{{ $user->id }}">{{ $user->full_name_extended }}</flux:select.option>
                @endforeach
            </flux:select>
            <div class="mt-2 flex flex-wrap gap-2">
                @foreach($selected_user_ids as $user_id)
                    @php $user = User::find($user_id) @endphp
                    <flux:badge
                            dismissable
                            wire:click="removeUser({{ $user_id }})"
                    >
                        {{ $user->full_name_extended }}
                    </flux:badge>
                @endforeach
            </div>
        </div>

        {{-- description --}}
        <div class="gap-4 mt-4">
            <flux:editor
                    class="h-full"
                    wire:model="description"
            />
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
            <flux:button wire:click="closeModal(['patient', 'appointment'])">{{ __('ehr.cancel') }}</flux:button>
        </div>
    </form>
</flux:modal>