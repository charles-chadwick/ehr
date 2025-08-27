<div class="text-xs p-2 m-2 text-white bg-red-700 rounded-sm">
    <p class="font-bold">The following users already have appointments at this time:</p>
    <ul role="list">
    @foreach($users as $user)
        <li>{{ $user->full_name_extended }}</li>
    @endforeach
    </ul>
</div>