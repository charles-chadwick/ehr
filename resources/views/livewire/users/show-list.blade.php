<?php

use Illuminate\Database\Eloquent\Collection;
use Livewire\Volt\Component;

new class extends Component {

    public Collection $users;

}; ?>

<div class="flex w-full flex-none justify-between items-center gap-x sm:w-auto">
    <flux:avatar.group>
    @foreach ($users as $user)
        <flux:dropdown
                align="end"
                gap="10"
                hover
                offset="-32"
                position="bottom"
                wire:key="user-{{ $user->id.'-'.uniqid() }}"
        >
            <button
                    class="flex items-center gap-3"
                    type="button"
            >
                <flux:avatar
                        circle
                        name="{{ $user->full_name }}"
                        size="sm"
                        src="{{ $user->avatar }}"
                />
            </button>

            <flux:popover class="flex flex-col rounded-xl shadow-xl">
                <div class="flex gap-2">
                    <flux:avatar
                            class="rounded-full"
                            name="{{ $user->full_name }}"
                            size="xl"
                            src="{{ $user->avatar }}"
                    />

                    <div>
                        <flux:heading size="lg">{{ $user->full_name }}</flux:heading>
                        <flux:text size="lg">{{ $user->role }}</flux:text>
                    </div>
                </div>

                <div class="flex gap-2 mt-2">
                    <flux:button
                            class="flex-1"
                            icon:class="opacity-75"
                            icon="check"
                            size="sm"
                            variant="outline"
                    >Visit Profile
                    </flux:button>
                    <flux:button
                            class="flex-1"
                            icon:class="opacity-75"
                            icon="chat-bubble-left-right"
                            size="sm"
                            variant="primary"
                    >Message
                    </flux:button>
                </div>
            </flux:popover>
        </flux:dropdown>
    @endforeach
    </flux:avatar.group>
</div>
