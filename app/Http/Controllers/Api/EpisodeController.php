<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ListEpisodesRequest;
use App\Services\Episode\EpisodeQueryService;
use Illuminate\Http\JsonResponse;

class EpisodeController extends Controller
{
    public function index(ListEpisodesRequest $request, EpisodeQueryService $episodeQueryService): JsonResponse
    {
        $episodes = $episodeQueryService
            ->list($request->validated())
            ->appends($request->query());

        return response()->json($episodes);
    }
}
