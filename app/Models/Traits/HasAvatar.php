<?php
/** @noinspection ALL */

namespace App\Models\Traits;

use Spatie\MediaLibrary\MediaCollections\Models\Media;

trait HasAvatar
{

    public function registerMediaConversions(?Media $media = null) : void
    {
        $this->addMediaConversion('avatar')
             ->fit(Fit::Contain, 300, 300)
             ->nonQueued();
    }

    public function registerMediaCollections() : void
    {
        $this->addMediaCollection('avatar')
             ->singleFile();
    }
}