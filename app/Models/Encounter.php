<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

class Encounter extends Base
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'date_of_service',
        'type',
        'title',
        'status',
        'signed_by',
        'signed_at',
        'content'
    ];

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

    protected function signedAt() : Attribute {
        return Attribute::make(
            get: fn($value) => Carbon::parse($value)->format(config('ehr.long_date_format'))
        );
    }

}
