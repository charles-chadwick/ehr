<?php

namespace App\Models;

use App\Enums\AppointmentStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Appointment extends Base
{
    use HasFactory;

    protected $guarded = ['id'];

    public function patient() : BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    protected function casts() : array
    {
        return [
            'date_and_time' => 'datetime',
            'status'        => AppointmentStatus::class,
            'length'        => 'integer',
        ];
    }
}
