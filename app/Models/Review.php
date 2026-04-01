<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends Model
{
    protected $fillable = [
        'episode_id',
        'author',
        'text',
        'published_at',
        'rating',
    ];

    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
            'rating' => 'decimal:1',
        ];
    }

    public function episode(): BelongsTo
    {
        return $this->belongsTo(Episode::class);
    }
}
