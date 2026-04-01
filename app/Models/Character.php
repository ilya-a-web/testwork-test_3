<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Character extends Model
{
    protected $fillable = [
        'external_id',
        'name',
        'gender',
        'status',
        'url',
    ];

    public function episodes(): BelongsToMany
    {
        return $this->belongsToMany(Episode::class, 'episode_character')->withTimestamps();
    }
}
