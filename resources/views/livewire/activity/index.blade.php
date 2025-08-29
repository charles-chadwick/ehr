<?php
/** @noinspection ALL */

use Carbon\Carbon;
use Livewire\Volt\Component;
use Spatie\Activitylog\Models\Activity;

new class extends Component {

    public $object;
    public $activities = [];

    public function mount($object) : void
    {
        $this->object = $object;
        $this->activities = Activity::where('subject_type', $object::class)
                              ->orderBy('created_at', 'DESC')
                              ->where('subject_id', $object->id)
                              ->get();
    }
}; ?>
<div class="divide-zinc-100 divide-y gap-y-4">
    @foreach($activities as $activity)
        <div class="py-2">On
            <span class="font-bold"></span>
            <a href="#" class="link">{{ $activity->causer->full_name_extended }}</a>
            {{ $activity->description }} this record.
        </div>
    @endforeach
</div>