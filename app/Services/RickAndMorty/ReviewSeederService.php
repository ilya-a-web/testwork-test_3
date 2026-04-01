<?php

namespace App\Services\RickAndMorty;

use App\Models\Episode;
use App\Services\Rating\ReviewRatingService;
use Faker\Factory;
use Illuminate\Support\Carbon;

class ReviewSeederService
{
    public function __construct(private readonly ReviewRatingService $ratingService)
    {
    }

    public function seedForEpisode(Episode $episode): int
    {
        if ($episode->reviews()->exists()) {
            return 0;
        }

        $texts = $this->loadReviewTexts();
        if ($texts === []) {
            return 0;
        }

        $min = max(1, (int) config('rickmorty.reviews_seed_min', 50));
        $max = max($min, (int) config('rickmorty.reviews_seed_max', 500));
        $count = random_int($min, $max);
        $faker = Factory::create();

        $rows = [];
        for ($i = 0; $i < $count; $i++) {
            $text = $texts[array_rand($texts)];

            $rows[] = [
                'episode_id' => $episode->id,
                'author' => $faker->name(),
                'text' => $text,
                'published_at' => Carbon::now()->subDays(random_int(0, 365)),
                'rating' => $this->ratingService->calculate($text),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        $episode->reviews()->insert($rows);

        return $count;
    }

    private function loadReviewTexts(): array
    {
        $path = (string) config('rickmorty.reviews_json_path', 'reviews.json');
        $fullPath = storage_path('app/'.$path);
        if (! is_file($fullPath)) {
            $fullPath = storage_path('app/private/'.$path);
        }
        if (! is_file($fullPath)) {
            return [];
        }

        $decoded = json_decode((string) file_get_contents($fullPath), true);

        return is_array($decoded) ? array_values(array_filter($decoded, 'is_string')) : [];
    }
}
