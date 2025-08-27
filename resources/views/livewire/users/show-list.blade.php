<?php

use Illuminate\Database\Eloquent\Collection;
use Livewire\Volt\Component;

new class extends Component {

    public Collection $users;

}; ?>

<div class="flex flex-wrap gap-2 mt-1">
    @foreach ($users as $user)
        <flux:avatar
                tooltip="{{ $user->full_name_extended }}"
                src="{{ $user->avatar }}"
                size="xs"
                class="shadow-xs border border-zinc-200"
        />
    @endforeach
</div>
