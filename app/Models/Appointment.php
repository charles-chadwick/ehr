<?php
/** @noinspection ALL */

namespace App\Models;

use App\Enums\AppointmentStatus;
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

    /**
     * Get the patient associated with the appointment.
     * @return BelongsTo
     */
    public function patient() : BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    /**
     * Get the users associated with the appointment.
     * @return BelongsToMany
     */
    public function users() : BelongsToMany
    {
        return $this->belongsToMany(User::class, 'appointments_users')
                    ->using(AppointmentUser::class)
                    ->withTimestamps()
                    ->withPivot('deleted_at')
                    ->wherePivotNull('deleted_at');
    }

    /**
     * Retrieves the formatted date attribute.
     *
     * @return string
     */
    public function getDateAttribute() : string
    {
        $start = $this->date_and_time;
        return $start->format(config('ehr.date_format'));
    }

    /**
     * Retrieves the formatted start time attribute.
     * @return string
     **/
    public function getStartAtAttribute() : string
    {
        $start = $this->date_and_time;
        return $start->format(config('ehr.time_format'));
    }

    /**
     * Retrieves the formatted end time attribute.
     * @return string
     **/
    public function getEndAtAttribute() : string
    {
        $start = $this->date_and_time;
        return $start->copy()
                     ->addMinutes((int) $this->length)
                     ->format(config('ehr.time_format'));
    }

    /**
     * Retrieves the formatted date and time range attribute.
     * @return string
     **/
    public function getDateAndTimeRangeAttribute() : string
    {
        return $this->date.' '.$this->start_at.' to '.$this->end_at;
    }

}
