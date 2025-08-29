<?php

namespace App\Models;

use App\Enums\NoteType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Note extends Base
{
    use HasFactory;

    protected $fillable = [
        'notable_id',
        'notable_type',
        'type',
        'title',
        'content',
    ];

    public function casts(): array
    {
        return [
            'type' => NoteType::class,
        ];
    }

    /**
     * Get the parent notable model.
     */
    public function notable(): MorphTo
    {
        return $this->morphTo();
    }
}
