<?php
/** @noinspection ALL */

use  App\Livewire\Traits\Sortable;
use App\Models\Patient;
use App\Models\PatientDx;
use Livewire\Attributes;
use Livewire\Attributes\Computed;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination, Sortable;

    public Patient $patient;

    #[Computed]
    #[On('diagnosis.index:refresh')]
    public function diagnoses()
    {
        return $this->patient->diagnoses()
            ->paginate(3);
    }
    #[On('diagnosis.index:refresh')]
    public function with()
    {
        return ['diagnoses' => $this->diagnoses];
    }
}; ?>

<ul class="list-group">
    @forelse($diagnoses as $diagnosis)
        <li
                class="list-item"
                wire:key="diagnosis-{{ $diagnosis->id }}"
        >
            <p class="font-semibold">
                ({{ $diagnosis->code }}) {{ $diagnosis->title }}
            </p>
            <div class="mt-1 flex items-center gap-x-2">
                <p>
                    {{ $diagnosis->created_at }}
                </p>
            </div>
        </li>
    @empty
        <li class="text-center">{{ __('ehr.no_records', ['items' => __('diagnosis.diagnoses')]) }}</li>
    @endforelse
    <flux:pagination :paginator="$diagnoses" />
</ul>
