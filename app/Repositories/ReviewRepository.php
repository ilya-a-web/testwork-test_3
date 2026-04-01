<?php

namespace App\Repositories;

use App\Models\Episode;
use App\Models\Review;

class ReviewRepository
{
    public function existsForEpisode(Episode $episode): bool
    {
        return $episode->reviews()->exists();
    }

    public function insertMany(array $rows): void
    {
        Review::query()->insert($rows);
    }

    public function createForEpisode(Episode $episode, string $author, string $text, float $rating): Review
    {
        return Review::query()->create([
            'episode_id' => $episode->id,
            'author' => $author,
            'text' => $text,
            'published_at' => now(),
            'rating' => $rating,
        ]);
    }
}
