<?php

namespace App\Models;

use App\Enums\PatientStatus;
use App\Models\Traits\HasAvatar;
use App\Models\Traits\IsPerson;
use Carbon\Carbon;
use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\MustVerifyEmail;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\Access\Authorizable;

class Patient extends Base implements AuthenticatableContract, AuthorizableContract, CanResetPasswordContract
{
    use Authenticatable, Authorizable, CanResetPassword, MustVerifyEmail;
    use HasAvatar, HasFactory, IsPerson;

    protected $fillable = [
        'status',
        'prefix',
        'first_name',
        'middle_name',
        'last_name',
        'suffix',
        'nickname',
        'date_of_birth',
        'gender',
        'gender_identity',
        'email',
        'password',
        'id',
    ];

    protected function casts(): array
    {
        return [
            // Keep the cast, but with dateFormat set above, it won't query the DB connection.
            'date_of_birth' => 'date',
            'status' => PatientStatus::class,
        ];
    }

    /**
     * Get the appointments for the patient.
     */
    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    /**
     * Define a many-to-many relationship with the Diagnosis model.
     */
    public function diagnoses(): BelongsToMany
    {
        return $this->belongsToMany(Diagnosis::class, 'patient_dxs')
            ->using(PatientDx::class)
            ->withTimestamps()
            ->withPivot('deleted_at')
            ->wherePivotNull('deleted_at');

    }

    /**
     * Get the patient's date of birth in the format specified in the config.'
     */
    public function getDOBAttribute(): string
    {
        return Carbon::parse($this->attributes['date_of_birth'])->format(config('ehr.dob_format'));
    }

    /**
     * Get the patient's age in the past years and months.
     **/
    public function getAgeAttribute(): string
    {
        $now = Carbon::now();
        $birth = Carbon::parse($this->date_of_birth);
        $months = ($now->month < $birth->month) ? $now->month + 12 - $birth->month : $now->month - $birth->month;

        return $birth->age.' years, '.$months.' months';
    }
}
