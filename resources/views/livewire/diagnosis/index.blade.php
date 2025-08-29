<?php
/** @noinspection ALL */

use App\Livewire\Traits\Sortable;
use App\Models\Patient;
use Carbon\Carbon;
use Livewire\Attributes\Computed;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination, Sortable;

    public Patient $patient;

    #[Computed]
    #[On("diagnosis.index:refresh")]
    public function diagnoses()
    {
        return $this->patient->diagnoses()
            ->paginate(3);
    }

    #[On("diagnosis.index:refresh")]
    public function with()
    {
        return ['diagnoses' => $this->diagnoses];
    }
}; ?>

<ul class="list-group">
    @forelse($diagnoses as $diagnosis)
        <li
                class="list-item flex-row"
                wire:key="diagnosis-{{ $diagnosis->id }}"
        >
            <a
                    class="link"
                    href="#"
            >
                ({{ $diagnosis->code }}) {{ $diagnosis->title }}
            </a>
            <p class="flex-none">
                {{ Carbon::parse($diagnosis->pivot->created_at)->format(config('ehr.date_format')) }}
            </p>
        </li>
    @empty
        <li class="text-center">{{ __('ehr.no_records', ['items' => __('diagnosis.diagnoses')]) }}</li>
    @endforelse
    <flux:pagination :paginator="$diagnoses" />
</ul>
