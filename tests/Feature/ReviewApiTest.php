<?php

namespace Tests\Feature;

use App\Models\Episode;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReviewApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_add_review_to_episode_and_rating_is_generated(): void
    {
        $episode = Episode::query()->create([
            'external_id' => 1,
            'name' => 'Pilot',
            'air_date' => '2013-12-02',
            'season' => 1,
            'episode' => 1,
            'code' => 'S01E01',
        ]);

        $response = $this->postJson("/api/episodes/{$episode->id}/reviews", [
            'author' => 'John Doe',
            'text' => 'Очень крутой эпизод!',
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.episode_id', $episode->id)
            ->assertJsonPath('data.author', 'John Doe');

        $rating = (float) data_get($response->json(), 'data.rating');

        $this->assertGreaterThanOrEqual(1.0, $rating);
        $this->assertLessThanOrEqual(5.0, $rating);
    }
}
