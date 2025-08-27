<?php

namespace App\Livewire\Traits;

use App\Models\Patient;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;

trait HasAvatarUpload {

    public $avatar_path = "";
    public $avatar_url = "";

    #[On('file-uploaded')]
    public function onFileUploaded(string $path, ?string $url = null) : void
    {
        $this->avatar_path = $path;
        $this->avatar_url = $url ?? '';
    }

    public function removeImage() : void
    {
        // do this the stupid way
        DB::table('media')
          ->where('model_type', Patient::class)
          ->where('model_id', $this->patient->id)
          ->delete();
        $this->avatar_url = "";
        $this->avatar_path = "";

    }

}