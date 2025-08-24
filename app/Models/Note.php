<?php

namespace App\Models;

use App\Enums\NoteType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Note extends Base
{
    use HasFactory;

    protected $fillable = [
        'type',
        'title',
        'content',
        'notable_id',
        'notable_type'
    ];

    public function casts() : array
    {
        return [
            'type' => NoteType::class,
        ];
    }

    public function notable() : MorphTo
    {
        return $this->morphTo();
    }
}
