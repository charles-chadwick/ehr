<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class AppointmentUser extends Pivot
{
    protected $table = 'appointments_users';

    protected $fillable = [
        'appointment_id',
        'user_id',
    ];

    public function appointment() : BelongsTo {
        return $this->belongsTo(Appointment::class);
    }

    public function user() : BelongsTo {
        return $this->belongsTo(User::class);
    }

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

    //
}
