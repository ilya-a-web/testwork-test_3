<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreReviewRequest;
use App\Models\Episode;
use App\Services\Review\ReviewService;
use Illuminate\Http\JsonResponse;

class ReviewController extends Controller
{
    public function store(StoreReviewRequest $request, Episode $episode, ReviewService $reviewService): JsonResponse
    {
        $validated = $request->validated();

        $review = $reviewService->create($episode, $validated['author'], $validated['text']);

        return response()->json([
            'data' => $review,
        ], 201);
    }
}
