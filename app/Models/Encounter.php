<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Encounter extends Base
{
    use HasFactory;

    protected $guarded = ['id'];

    public function patient() : BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function signedBy() : BelongsTo
    {
        return $this->belongsTo(User::class, 'signed_by');
    }

    protected function casts() : array
    {
        return [
            'date_of_service' => 'datetime',
        ];
    }

}
