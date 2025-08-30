<?php

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Modelable;
use Livewire\Volt\Component;

new class extends Component {

    #[Modelable]
    public array      $selected_user_ids = [];
    public Collection $user_list;

    public function mount() : void
    {
        $this->user_list = User::staff()
            ->get();
    }

    public function removeUser(int $id) : void
    {
        $this->selected_user_ids = array_values(array_filter($this->selected_user_ids,
            fn(int $userId) => $userId !== $id));
    }
}; ?>

{{-- user selection --}}
<div>
    <flux:select
        label="{{ __('users.users') }}"
        multiple
        placeholder="{{ __('users.choose') }}"
        searchable
        variant="listbox"
        wire:model.live="selected_user_ids"
    >
        @foreach($user_list as $user)
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