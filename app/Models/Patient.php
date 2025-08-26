<?php

namespace App\Models;

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
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Foundation\Auth\Access\Authorizable;

class Patient extends Base implements AuthenticatableContract, AuthorizableContract, CanResetPasswordContract
{
    use Authenticatable, Authorizable, CanResetPassword, MustVerifyEmail;
    use HasFactory, HasAvatar, IsPerson;

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
        'password'
    ];

    protected function casts() : array
    {
        return [
            'date_of_birth' => 'date',
        ];
    }

    public function getAgeAttribute() : string
    {
        $now = Carbon::now();
        $birth = Carbon::parse($this->date_of_birth);
        $months = ($now->month < $birth->month) ? $now->month + 12 - $birth->month : $now->month - $birth->month;
        return $birth->age.' years '.$months.' months';
    }

    public function notes() : MorphMany
    {
        return $this->morphMany(Note::class, 'notable', 'notable_type', 'notable_id');
    }
}
