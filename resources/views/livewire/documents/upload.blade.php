<?php

use Livewire\Volt\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

new class extends Component {

    use WithFileUploads;

    // File bound from the input
    public $document = null;

    // Configurable props (can be set from parent)
    public string $disk      = 'public';
    public string $directory = 'uploads';
    public string $event     = 'file-uploaded';
    public ?string $label    = 'Upload file';
    public ?string $accept   = null;

    public function mount() {}

    public function updatedDocument() : void
    {
        $this->validate([
            'document' => 'required|file|max:5120', // ~5MB
        ]);

        $path = $this->document->store($this->directory, $this->disk);
        $url  = Storage::disk($this->disk)->url($path);

        // Emit a global event that parents can listen to
        $this->dispatch($this->event, path: $path, url: $url);
    }
}; ?>

<div>
    <flux:input
            type="file"
            wire:model="document"
            :accept="$accept"
            label="{{ $label }}"
    />
</div>
