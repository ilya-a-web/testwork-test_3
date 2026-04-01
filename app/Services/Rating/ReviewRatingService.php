<?php

namespace App\Services\Rating;

class ReviewRatingService
{
    public function calculate(string $text): float
    {
        $strategy = config('review_rating.strategy', 'random');

        return match ($strategy) {
            'sentiment' => $this->sentimentRating($text),
            default => $this->randomRating(),
        };
    }

    private function randomRating(): float
    {
        return round(random_int(10, 50) / 10, 1);
    }

    private function sentimentRating(string $text): float
    {
        $compound = null;

        // Compatible with php-sentiment-analyzer style output containing "compound".
        if (class_exists('Sentiment\\Analyzer')) {
            /** @var object $analyzer */
            $analyzer = new \Sentiment\Analyzer();

            if (method_exists($analyzer, 'getSentiment')) {
                $result = $analyzer->getSentiment($text);
                $compound = is_array($result) ? ($result['compound'] ?? null) : null;
            }
        }

        if (! is_numeric($compound)) {
            return $this->randomRating();
        }

        $normalized = max(-1, min(1, (float) $compound));

        return round((($normalized + 1) / 2) * 4 + 1, 1);
    }
}
