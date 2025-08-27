<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Carbon\Carbon;
use App\Models\User;

class AppointmentUser extends Pivot
{
    protected $table = 'appointments_users';

    protected $fillable = [
        'appointment_id',
        'user_id',
    ];

    /**
     * Get the appointment associated with the user.
     * @return BelongsTo
     */
    public function appointment() : BelongsTo {
        return $this->belongsTo(Appointment::class);
    }

    /**
     * Get the user associated with the appointment.
     * @return BelongsTo
     */
    public function user() : BelongsTo {
        return $this->belongsTo(User::class);
    }

    /**
     * Sync the users associated with the appointment.
     * @param $appointment_id
     * @param $user_ids
     * @return void
     */
    public function syncUsers($appointment_id, $user_ids) : void
    {
        // get the user ids associated with this appointment
        $this->where('appointment_id', $appointment_id)
             ->whereNotIn('user_id', $user_ids)
             ->delete();

        collect($user_ids)->map(function ($user_id) use ($appointment_id) {
            $this->updateOrCreate([
                'appointment_id' => $appointment_id,
                'user_id'        => $user_id,
            ], [
                'appointment_id' => $appointment_id,
                'user_id'        => $user_id,
            ]);
        });


    }

    /**
     * Check if any of the given users are scheduled at the specified date/time.
     * Returns false if none, or a list of conflicting users if there are any.
     * @param array $user_ids
     * @param Carbon|string $date_time
     * @param int $length_minutes
     * @return bool|array
     */
    public function checkScheduleConflicts(array $user_ids, Carbon|string $date_time, int $length_minutes): bool|array
    {
        $start_at = $date_time instanceof Carbon ? $date_time->copy() : Carbon::parse($date_time);
        $end_at = $start_at->copy()->addMinutes($length_minutes);

        $conflicting_users = User::query()
            ->whereIn('users.id', $user_ids)
            ->whereHas('appointments', function ($query) use ($start_at, $end_at) {
                $query->where('date_and_time', '<', $end_at)
                      ->whereRaw('DATE_ADD(date_and_time, INTERVAL length MINUTE) > ?', [$start_at]);
            })
            ->get();

        return $conflicting_users->isEmpty() ? false : $conflicting_users->all();
    }

    //
}
