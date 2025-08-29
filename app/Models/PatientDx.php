<?php

namespace App\Models;

use App\Models\Traits\TracksUsers;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class PatientDx extends Pivot
{
     use TracksUsers;

    protected $table = 'patient_dxs';

    protected $fillable = [
        'patient_id',
        'diagnosis_id',
        'status',
        'notes',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    /**
     * Define an inverse one-to-one or many relationship with the Patient model.
     *
     * @return BelongsTo
     */
    public function patient() : BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    /**
     * Establish an inverse one-to-one or many relationship with the Diagnosis model.
     *
     * @return BelongsTo
     */
    public function diagnosis() : BelongsTo
    {
        return $this->belongsTo(Diagnosis::class);
    }
}
