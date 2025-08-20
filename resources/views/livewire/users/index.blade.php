<?php

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\On;
use Livewire\Volt\Component;

new class extends Component {

    public $users;


    #[On("users.index:refresh")]
    public function mount() : void
    {
        $this->users = User::get();
    }


    public function with() : array
    {
        return ['users' => $this->users];
    }
}; ?>

<div>
    <flux:card size="sm">
        <slot:title class="font-bold text-zinc-700">
            <div class="flex justify-between">
                <h3 class="font-bold">Users</h3>
                <flux:modal.trigger name="user-form">
                    <flux:button>Create New User</flux:button>
                </flux:modal.trigger>
            </div>

        </slot:title>
        @foreach($users as $user)
            <div
                    wire:key="{{ $user->id }}"
                    class="flex justify-between px-2 py-4"
            >
                <div class="w-auto">
                    <div class="flex justify-between px-2 py-4">
                        <flux:avatar
                                class="flex-none w-16 h-16 rounded-full mr-4"
                                src="{{ $user->avatar }}"
                                alt="{{ $user->full_name_extended }}"
                                title="{{ $user->full_name_extended }}"
                                size="sm"
                        />
                        <div class="w-auto">
                            <h3 class="font-semibold text-zinc-800">{{ $user->full_name_extended }}</h3>
                            <p class="text-sm text-zinc-700">{{ $user->role }} | {{ $user->email }}</p>
                        </div>
                    </div>
                </div>
                <div class="shrink-0">
                    <flux:dropdown
                            position="bottom"
                            align="end"
                    >
                        <flux:button size="sm">...</flux:button>
                        <flux:navmenu>

                            <flux:navmenu.item
                                    href="#"
                                    icon="user"
                            >
                                <flux:modal.trigger
                                        name="user-form"
                                        wire:click="$dispatch('edit-user', {id: {{ $user->id }}})"
                                >Account
                                </flux:modal.trigger>
                            </flux:navmenu.item>

                            <flux:navmenu.item
                                    href="#"
                                    icon="trash"
                                    variant="danger"
                            >Delete
                            </flux:navmenu.item>
                        </flux:navmenu>
                    </flux:dropdown>
                </div>
            </div>
        @endforeach
    </flux:card>
    <flux:modal name="user-form">
        <livewire:users.form modal="user-form" />
    </flux:modal>
</div>
