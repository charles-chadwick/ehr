<?php

namespace App\Models;

use App\Enums\AppointmentStatus;
use Carbon\Carbon;
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

    public function getFullDateAndTimeAttribute() : string {
        return Carbon::parse($this->date_and_time)->format('m/d/Y h:ia').' to '.
               Carbon::parse($this->date_and_time)->addMinutes($this->length)->format('h:ia');
    }
}
