<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Episode extends Model
{
    protected $fillable = [
        'external_id',
        'name',
        'air_date',
        'season',
        'episode',
        'code',
    ];

    protected function casts(): array
    {
        return [
            'air_date' => 'date',
        ];
    }

    public function characters(): BelongsToMany
    {
        return $this->belongsToMany(Character::class, 'episode_character')->withTimestamps();
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }
}
