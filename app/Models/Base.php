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

    // Merge base defaults with the child model's fillable
    public function getFillable(): array
    {
        return array_values(array_unique(array_merge([
            'created_by',
            'updated_by',
            'deleted_by',
        ], parent::getFillable() // returns the child model's $fillable
        )));
    }

    /**
     * Define a relationship to the user who created the entity.
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Define a relationship to the user who last updated the entity.
     */
    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Define a relationship indicating which user deleted this entity.
     */
    public function deletedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    /**
     * Accessor for formatting the created_at attribute.
     */
    public function createdAt(): Attribute
    {
        return Attribute::make(get: fn ($value) => $value ? Carbon::parse($value)
            ->format(config('ehr.date_and_time_format')) : null);
    }

    /**
     * Accessor for the updated_at attribute, formatting the value
     * using the specified date format if it exists.
     */
    public function updatedAt(): Attribute
    {
        return Attribute::make(get: fn ($value) => $value ? Carbon::parse($value)
            ->format(config('ehr.date_and_time_format')) : null);
    }

    /**
     * Accessor to format the deleted at timestamp.
     */
    public function deletedAt(): Attribute
    {
        return Attribute::make(get: fn ($value) => $value ? Carbon::parse($value)
            ->format(config('ehr.date_and_time_format')) : null);
    }

    /**
     * Configure the activity log options for the model.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logExcept([
                'updated_at',
                'created_at',
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('Database');
    }
}
