<?php

use Illuminate\Database\Eloquent\Collection;
use Livewire\Volt\Component;

new class extends Component {

    public Collection $users;

}; ?>

<div>
@foreach ($users as $user)
    <span wire:model="{{ $user->id }}">
        <span>{{ $user->full_name }}</span>
    </span>
    @endforeach
</div>
