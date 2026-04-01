<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Episode;
use App\Models\Review;
use App\Services\Rating\ReviewRatingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function store(Request $request, Episode $episode, ReviewRatingService $ratingService): JsonResponse
    {
        $validated = $request->validate([
            'author' => ['required', 'string', 'max:255'],
            'text' => ['required', 'string', 'max:5000'],
        ]);

        $review = Review::query()->create([
            'episode_id' => $episode->id,
            'author' => $validated['author'],
            'text' => $validated['text'],
            'published_at' => now(),
            'rating' => $ratingService->calculate($validated['text']),
        ]);

        return response()->json([
            'data' => $review,
        ], 201);
    }
}
