<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Base extends Model implements HasMedia {

    use InteractsWithMedia, LogsActivity, SoftDeletes;

    public function __construct($attributes = [])
    {
        parent::__construct($attributes);
    }

    public function getActivitylogOptions() : LogOptions
    {
        return LogOptions::defaults()
                         ->logAll()
                         ->logExcept([
                             'updated_at',
                             'created_at'
                         ])
                         ->logOnlyDirty()
                         ->dontSubmitEmptyLogs()
                         ->useLogName('Database');
    }
}
