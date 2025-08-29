<?php

/** @noinspection ALL */

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

trait HasAvatar
{
    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('avatars')
            ->fit(Fit::Contain, 300, 300)
            ->nonQueued();
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('avatars')
            ->singleFile();
    }

    public function avatar(): Attribute
    {
        return Attribute::make(
            get: function () {
                return $this->getFirstMediaUrl('avatars');
            }
        );
    }
}
