<?php

namespace App\Services\Review;

use App\Models\Episode;
use App\Models\Review;
use App\Repositories\ReviewRepository;
use App\Services\Rating\ReviewRatingService;

class ReviewService
{
    public function __construct(
        private readonly ReviewRepository $reviewRepository,
        private readonly ReviewRatingService $ratingService,
    ) {
    }

    public function create(Episode $episode, string $author, string $text): Review
    {
        $rating = $this->ratingService->calculate($text);

        return $this->reviewRepository->createForEpisode($episode, $author, $text, $rating);
    }
}
