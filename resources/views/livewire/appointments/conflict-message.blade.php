<flux:callout
        class="mb-4"
        heading="{{ __('appointments.schedule_conflict') }}"
        icon="exclamation-circle"
        variant="danger"
>
    <flux:callout.text>
        <ul role="list">
            @foreach($users as $user)
                <li>{{ $user->full_name_extended }}</li>
        @endforeach

    </flux:callout.text>
</flux:callout>