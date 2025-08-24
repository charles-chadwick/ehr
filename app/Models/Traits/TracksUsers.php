<?php
/** @noinspection ALL */

namespace App\Models\Traits;

use Illuminate\Support\Facades\Auth;

trait TracksUsers
{
    public static function bootTracksUsers() : void
    {
        static::creating(function ($model) {
            if (!Auth::check()) {
                return;
            }

            if ($model->isFillable('created_by') && empty($model->getAttribute('created_by'))) {
                $model->setAttribute('created_by', Auth::id());
            }

            if ($model->isFillable('updated_by')) {
                $model->setAttribute('updated_by', Auth::id());
            }
        });

        static::updating(function ($model) {
            if (Auth::check() && $model->isFillable('updated_by')) {
                $model->setAttribute('updated_by', Auth::id());
            }
        });

        static::deleting(function ($model) {
            if (Auth::check() && $model->isFillable('deleted_by')) {
                $model->setAttribute('deleted_by', Auth::id());
                // Persist blame without firing additional events
                $model->saveQuietly();
            }
        });
    }
}