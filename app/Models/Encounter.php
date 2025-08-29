<?php

namespace App\Models;

use App\Enums\EncounterStatus;
use App\Enums\EncounterType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
        'content',
    ];

    protected function casts(): array
    {
        return [
            'date_of_service' => 'datetime',
            'type' => EncounterType::class,
            'status' => EncounterStatus::class,
            'signed_at' => 'datetime',
        ];
    }

    /**
     * Define a relationship to the patient.
     */
    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    /**
     * Define a relationship to the user who signed the encounter.
     */
    public function signedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'signed_by');
    }
}
