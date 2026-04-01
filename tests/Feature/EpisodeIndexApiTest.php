<?php

namespace Tests\Feature;

use App\Models\Character;
use App\Models\Episode;
use App\Models\Review;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EpisodeIndexApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_filter_by_season_and_sort_by_avg_rating(): void
    {
        $character = Character::query()->create([
            'external_id' => 1,
            'name' => 'Rick Sanchez',
            'gender' => 'Male',
            'status' => 'жив',
            'url' => 'https://rickandmortyapi.com/api/character/1',
        ]);

        $episodeOne = Episode::query()->create([
            'external_id' => 1,
            'name' => 'Pilot',
            'air_date' => '2013-12-02',
            'season' => 1,
            'episode' => 1,
            'code' => 'S01E01',
        ]);

        $episodeTwo = Episode::query()->create([
            'external_id' => 2,
            'name' => 'Lawnmower Dog',
            'air_date' => '2013-12-09',
            'season' => 1,
            'episode' => 2,
            'code' => 'S01E02',
        ]);

        $episodeOne->characters()->attach($character->id);
        $episodeTwo->characters()->attach($character->id);

        Review::query()->create([
            'episode_id' => $episodeOne->id,
            'author' => 'A',
            'text' => 'ok',
            'published_at' => now(),
            'rating' => 2.0,
        ]);

        Review::query()->create([
            'episode_id' => $episodeTwo->id,
            'author' => 'B',
            'text' => 'great',
            'published_at' => now(),
            'rating' => 5.0,
        ]);

        $response = $this->getJson('/api/episodes?season=1&sort_by=avg_rating&sort_dir=desc&character_ids=1');

        $response->assertOk();
        $response->assertJsonPath('data.0.id', $episodeTwo->id);
        $response->assertJsonPath('data.1.id', $episodeOne->id);
    }
}
