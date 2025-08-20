<?php

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Validation\Rule;
use Livewire\Attributes\On;
use Livewire\Volt\Component;
use Flux\Flux;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileDoesNotExist;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileIsTooBig;
use Illuminate\Support\Facades\DB;

new class extends Component {

    public ?User  $user;
    public string $role                  = "";
    public string $prefix                = "";
    public string $first_name            = "";
    public string $last_name             = "";
    public string $suffix                = "";
    public string $email                 = "";
    public string $password              = "";
    public string $password_confirmation = "";
    public string $modal                 = "";

    public string $avatar_path = "";
    public string $avatar_url  = "";

    public function mount(?User $user) : void
    {
        $this->user = $user;
        if ($this->user?->id) {
            $this->loadUser($this->user->id);

        }
    }

    #[On("edit-user")]
    public function loadUser($id) : void
    {
        $this->user = User::findOrFail($id);
        $this->fill($this->user);
        $this->avatar_url = $this->user->avatar;
    }

    #[On('file-uploaded')]
    public function onFileUploaded(string $path, ?string $url = null) : void
    {
        $this->avatar_path = $path;
        $this->avatar_url = $url ?? '';
    }

    public function removeImage() : void
    {
        DB::table('media')
          ->where('model_type', User::class)
          ->where('model_id', $this->user->id)
          ->delete();
        $this->avatar_url = "";
        $this->avatar_path = "";

    }

    public function save() : void
    {
        $validated = $this->validate();

        $user_data = [
            'prefix'     => $this->prefix,
            'first_name' => $this->first_name,
            'last_name'  => $this->last_name,
            'suffix'     => $this->suffix,
            'role'       => $this->role,
            'email'      => $this->email,
        ];

        if (!empty($validated['password'] ?? null)) {
            $user_data['password'] = bcrypt($validated['password']);
        }

        if ($this->user?->id) {

            $this->user->update($user_data);
            $message = "Successfully updated user";
            $heading = "User updated";
        } else {

            $this->user = User::create($user_data);
            $message = "Successfully created user";
            $heading = "User created";
        }

        if ($this->avatar_path !== "") {
            try {
                $this->user->addMedia(storage_path('app/public/'.$this->avatar_path))
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
        $this->dispatch("users.index:refresh");
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
            'role'                  => [
                'required',
                Rule::in(UserRole::cases())
            ],
            'email'                 => 'required|email',
            'password'              => $this->user->id ? 'nullable' : 'required'.'|string|min:1|max:255|confirmed',
            'password_confirmation' => $this->user->id ? 'nullable' : 'required'.'|string|min:1|max:255'
        ];
    }

    public function with() : array
    {
        return [
            'user' => $this->user
        ];
    }

}; ?>

<div>
    <div class="flex gap-4 py-4">
        <div class="w-1/6">
            <flux:input
                    label="Prefix"
                    placeholder="Dr."
                    wire:model="prefix"
            />
        </div>
        <div class="w-auto">
            <flux:input
                    label="First Name"
                    placeholder="Jane"
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
                    placeholder="M.D."
                    wire:model="suffix"
            />
        </div>

    </div>
    <div class="flex gap-4 py-4">
        <div class="w-1/2">
            <flux:input
                    label="Email"
                    placeholder="jane@doe.com"
                    wire:model="email"
            />
        </div>
        <div class="w-1/2">
            <flux:select
                    label="Role"
                    variant="listbox"
                    placeholder="Choose Role"
                    wire:model="role"
            >
                @foreach(UserRole::cases() as $user_role)
                    <flux:select.option>{{ $user_role }}</flux:select.option>
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
        <flux:callout.text class="text-xs text-center">Only fill out these fields if you are setting or changing the password.</flux:callout.text>

    <div
            id="avatar"
    >
        <div class="mt-4">

            @if($avatar_url)
                <flux:label for="avatar">Avatar</flux:label>

                <flux:avatar
                        class="flex-none w-24 h-24  rounded-full object-cover mr-4"
                        src="{{ $avatar_url }}"
                        alt="{{ $user->full_name_extended }}"
                        title="{{ $user->full_name_extended }}"
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
