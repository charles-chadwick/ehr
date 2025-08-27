<?php

use Illuminate\Database\Eloquent\Collection;
use Livewire\Volt\Component;

new class extends Component {

    public Collection $users;

}; ?>

<dl class="flex w-full flex-none justify-between gap-x-1 sm:w-auto">
    @foreach ($users as $user)
        <dd wire:key="user-{{ $user->id.'-'.uniqid() }}">
            <img
                    src="{{ $user->avatar }}"
                    alt="Emma Dorsey"
                    class="size-6 rounded-full bg-gray-50 outline-2 outline-white dark:bg-gray-800 dark:outline-gray-900"
            />
        </dd>
    @endforeach


</dl>
