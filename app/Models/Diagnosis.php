<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Diagnosis extends Base
{
    use HasFactory;

    protected $table = 'diagnosis';

    public $fillable = [
        'set',
        'code',
        'title',
        'description',
    ];
}
