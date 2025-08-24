<?php

namespace App\Models;

class AppointmentUser extends Base
{
    protected $table = 'appointments_users';

    protected $fillable = [
        'appointment_id',
        'user_id',
    ];

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
