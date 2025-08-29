<?php

use App\Models\Diagnosis;
use App\Models\Patient;
use App\Models\PatientDx;
use Flux\Flux;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Volt\Component;

new class extends Component {

    public const  SEARCH_FIELDS = [
        'code',
        'title'
    ];

    public Patient    $patient;
    public string     $search       = '';
    public            $diagnosis_id = null;
    public Collection $selected_diagnoses;
    public string $modal;

    public function mount(Patient $patient) : void
    {
        $this->patient = $patient;
        $this->selected_diagnoses = collect([]);
    }

    #[Computed]
    public function diagnoses() : Collection
    {
        return Diagnosis::when($this->search, function (Builder $query) {
            $query->whereAny(self::SEARCH_FIELDS, 'like', '%'.$this->search.'%');
        })
            ->when(isset($this->selected_diagnoses) && $this->selected_diagnoses->count() > 0,
                function (Builder $query) {

                    // ignore selected diagnoses
                    $query->whereNotIn('id', $this->selected_diagnoses->map(function ($diagnosis) {
                        return $diagnosis->id;
                    }));
                })
            ->limit(20)
            ->get();
    }

    #[On('diagnosis.search:select')]
    public function select($id) : void
    {
        $this->selected_diagnoses[] = Diagnosis::find($id);
    }

    public function save() : void
    {
        $this->selected_diagnoses->each(function (Diagnosis $diagnosis) {
            PatientDx::create([
                'patient_id'   => $this->patient->id,
                'diagnosis_id' => $diagnosis->id,
            ]);
        });

        Flux::modal($this->modal)->close();
        // set the error messages
        $message = __('ehr.success_message', ['item' => __('diagnosis.diagnoses')]);
        $heading = __('ehr.success_heading');
        $variant = "success";
        Flux::toast($message, heading: $heading, variant: $variant);
        $this->dispatch('diagnosis.index:refresh');
    }

    public function remove($id) : void
    {
        $this->selected_diagnoses = collect($this->selected_diagnoses)
            ->filter(function (Diagnosis $diagnosis) use ($id) {
                return $diagnosis->id !== $id;
            });
    }
}; ?>

<form wire:submit="save">
    <div class="space-y-2">
        <flux:select
                wire:model="diagnosis_id"
                variant="combobox"
        >
            <x-slot name="input">
                <flux:select.input wire:model.live="search" />
            </x-slot>

            @if(count($this->diagnoses) > 0)
                @foreach ($this->diagnoses as $diagnosis)
                    <flux:select.option
                            value="diagnosis-{{ $diagnosis->id }}"
                            wire:click="$dispatch('diagnosis.search:select', {id: {{ $diagnosis->id }}})"
                            wire:key="{{ $diagnosis->id }}"
                    >            {{ $diagnosis->title }} ({{ $diagnosis->code }})
                    </flux:select.option>
                @endforeach
            @endif
        </flux:select>
        @if(count($selected_diagnoses) > 0)
            <div class="list-group">

                @foreach($selected_diagnoses as $selected_diagnosis)
                    <div class="list-item">
                        <div>
                            {{ $selected_diagnosis->title }} ({{ $selected_diagnosis->code }})
                        </div>
                        <flux:icon
                                name="x-circle"
                                class="hover:text-red-500 cursor-pointer"
                                wire:click="remove({{ $selected_diagnosis->id }})"
                        />
                    </div>
                @endforeach
            </div>
        @endif
        <div class="flex justify-center gap-2">
            <flux:button type="submit" variant="primary" color="emerald">
                Save
            </flux:button>

        </div>
    </div>
</form>
