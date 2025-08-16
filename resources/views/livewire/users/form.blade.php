<?php

use App\Models\User;
use Livewire\Volt\Component;

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

    public function mount(?User $user) : void
    {
        $this->user = $user;
        if ($this->user?->id) {
            $this->fill($user);
        }
    }

}; ?>

<flux:card class="space-y-4">
    <div class="flex flex-row gap-4 py-4">
        <div class="flex-none w-auto">
            <flux:input
                    label="Prefix"
                    placeholder="Dr."
                    wire:model="prefix"
            />
        </div>
        <div class="flex-1">
            <flux:input
                    label="First Name"
                    placeholder="Jane"
                    wire:model="first_name"
            />
        </div>
        <div class="flex-1">
            <flux:input
                    label="Last Name"
                    placeholder="Doe"
                    wire:model="last_name"
            />
        </div>
        <div class="flex-none">
            <flux:input
                    label="Suffix"
                    placeholder="M.D."
                    wire:model="suffix"
            />
        </div>
    </div>
</flux:card>
