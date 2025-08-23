<?php
namespace App\Models\Pivots;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\SoftDeletes;

class AppointmentUser extends Pivot
{
    use SoftDeletes;

    protected $table = 'appointments_users';

    // The table has its own "id" primary key.
    public $incrementing = true;

    protected $dates = [
        'deleted_at',
    ];
}
