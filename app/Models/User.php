<?php

namespace App\Models;

use App\Enums\UserRole;
use App\Models\Traits\HasAvatar;
use App\Models\Traits\IsPerson;
use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\MustVerifyEmail;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Notifications\Notifiable;

class User extends Base implements AuthenticatableContract, AuthorizableContract, CanResetPasswordContract
{
    use Authenticatable, Authorizable, CanResetPassword, MustVerifyEmail;
    use HasAvatar, HasFactory, IsPerson, Notifiable;

    protected $guarded = [
        'id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function scopeStaff($query)
    {
        return $query->where('role', '!=', UserRole::Admin->value);
    }

    /**
     * Define a many-to-many relationship with the Appointment model.
     * Uses a custom pivot model AppointmentUser.
     * Includes pivot columns 'deleted_at' and timestamps.
     * Filters results to exclude records with a non-null 'deleted_at' value.
     */
    public function appointments(): BelongsToMany
    {
        return $this->belongsToMany(Appointment::class, 'appointments_users')
            ->using(AppointmentUser::class)
            ->withTimestamps()
            ->withPivot('deleted_at')
            ->wherePivotNull('deleted_at');

    }
}
