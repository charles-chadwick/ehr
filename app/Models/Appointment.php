<?php

namespace App\Models;

use App\Enums\AppointmentStatus;
use App\Models\Pivots\AppointmentUser;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Appointment extends Base
{
    use HasFactory;

    protected $guarded = ['id'];

    private const DATE_FORMAT = 'm/d/Y h:ia';
    private const TIME_FORMAT = 'h:ia';

    public function patient() : BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function users() : BelongsToMany
    {
        return $this->belongsToMany(User::class, 'appointments_users')
                    ->using(AppointmentUser::class)
                    ->withTimestamps()
                    ->withPivot('deleted_at')
                    ->wherePivotNull('deleted_at');
    }


    protected function casts() : array
    {
        return [
            'date_and_time' => 'datetime',
            'status'        => AppointmentStatus::class,
            'length'        => 'integer',
        ];
    }

    public function getEndAtAttribute() : Carbon
    {
        $start = $this->date_and_time; // cast to Carbon by Eloquent
        return $start->copy()
                     ->addMinutes((int) $this->length);
    }

    public function getFullDateAndTimeAttribute() : string
    {
        $start = $this->date_and_time; // already Carbon due to cast
        $end = $this->end_at;

        $start_formatted = $this->formatDateTime($start, self::DATE_FORMAT);
        $end_formatted = $this->formatDateTime($end, self::TIME_FORMAT);

        return $start_formatted.' to '.$end_formatted;
    }

    private function formatDateTime(CarbonInterface $dateTime, string $format) : string
    {
        return $dateTime->format($format);
    }
}
