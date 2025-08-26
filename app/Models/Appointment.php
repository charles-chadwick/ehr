<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Appointment extends Model
{
    use HasFactory, SoftDeletes;

    public function patient() : BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    protected function casts() : array
    {
        return [
            'date_and_time' => 'datetime',
        ];
    }
}
