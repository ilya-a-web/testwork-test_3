<?php

namespace App\Repositories;

use App\Models\Character;

class CharacterRepository
{
    public function upsert(array $rows): void
    {
        Character::query()->upsert($rows, ['external_id'], ['name', 'gender', 'status', 'url', 'updated_at']);
    }

    public function idByUrlMap(): array
    {
        return Character::query()->pluck('id', 'url')->all();
    }
}
