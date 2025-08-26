<?php

namespace App\Models;

use App\Enums\AppointmentStatus;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Appointment extends Base
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'date_and_time',
        'length',
        'status',
        'type',
        'title',
        'description',
    ];

    protected function casts() : array
    {
        return [
            'date_and_time' => 'datetime',
            'status'        => AppointmentStatus::class,
            'length'        => 'integer',
        ];
    }

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

    public function getEndAtAttribute() : Carbon
    {
        $start = $this->date_and_time;
        return $start->copy()
                     ->addMinutes((int) $this->length);
    }

    public function getFullDateAndTimeAttribute() : string
    {
        $start = $this->date_and_time;
        $end = $this->end_at;

        $start_formatted = $this->formatDateTime($start, config('ehr.date_format').' '.config('ehr.time_format'));
        $end_formatted = $this->formatDateTime($end, config('ehr.time_format'));

        return $start_formatted.' to '.$end_formatted;
    }

    private function formatDateTime(CarbonInterface $dateTime, string $format) : string
    {
        return $dateTime->format($format);
    }
}
