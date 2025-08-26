<?php

namespace App\Models;

use App\Models\Traits\TracksUsers;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Base extends Model implements HasMedia
{
    use InteractsWithMedia, LogsActivity, SoftDeletes, TracksUsers;

    private const DATE_FORMAT = 'm/d/Y @ h:i A';

    // Merge base defaults with the child model's fillable
    public function getFillable() : array
    {
        return array_values(array_unique(array_merge([
                'created_by',
                'updated_by',
                'deleted_by',
            ], parent::getFillable() // returns the child model's $fillable
        )));
    }

    public function createdBy() : BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy() : BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function deletedBy() : BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    public function createdAt() : Attribute
    {
        return Attribute::make(get: fn($value) => $value ? Carbon::parse($value)
                                                                 ->format(self::DATE_FORMAT) : null);
    }

    public function updatedAt() : Attribute
    {
        return Attribute::make(get: fn($value) => $value ? Carbon::parse($value)
                                                                 ->format(self::DATE_FORMAT) : null);
    }

    public function deletedAt() : Attribute
    {
        return Attribute::make(get: fn($value) => $value ? Carbon::parse($value)
                                                                 ->format(self::DATE_FORMAT) : null);
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
