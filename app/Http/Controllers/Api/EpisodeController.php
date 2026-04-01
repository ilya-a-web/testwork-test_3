<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Episode;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EpisodeController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'character_ids' => ['nullable', 'string'],
            'character' => ['nullable', 'string', 'max:255'],
            'season' => ['nullable', 'integer', 'min:1'],
            'air_date_from' => ['nullable', 'date'],
            'air_date_to' => ['nullable', 'date'],
            'sort_by' => ['nullable', 'in:air_date,avg_rating'],
            'sort_dir' => ['nullable', 'in:asc,desc'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
            'page' => ['nullable', 'integer', 'min:1'],
        ]);

        $query = Episode::query()
            ->with([
                'characters:id,external_id,name,gender,status,url',
                'reviews:id,episode_id,author,text,published_at,rating',
            ])
            ->withAvg('reviews', 'rating');

        if (! empty($validated['season'])) {
            $query->where('season', (int) $validated['season']);
        }

        if (! empty($validated['air_date_from'])) {
            $query->whereDate('air_date', '>=', $validated['air_date_from']);
        }

        if (! empty($validated['air_date_to'])) {
            $query->whereDate('air_date', '<=', $validated['air_date_to']);
        }

        if (! empty($validated['character_ids'])) {
            $ids = collect(explode(',', (string) $validated['character_ids']))
                ->map(fn (string $id): int => (int) trim($id))
                ->filter(fn (int $id): bool => $id > 0)
                ->values();

            if ($ids->isNotEmpty()) {
                $query->whereHas('characters', function (Builder $builder) use ($ids): void {
                    $builder->whereIn('characters.external_id', $ids);
                });
            }
        }

        if (! empty($validated['character'])) {
            $value = (string) $validated['character'];

            $query->whereHas('characters', function (Builder $builder) use ($value): void {
                $builder->where('name', 'like', "%{$value}%");
            });
        }

        $sortBy = $validated['sort_by'] ?? 'air_date';
        $sortDir = $validated['sort_dir'] ?? 'desc';

        if ($sortBy === 'avg_rating') {
            $query->orderBy('reviews_avg_rating', $sortDir);
        } else {
            $query->orderBy('air_date', $sortDir);
        }

        $query->orderBy('id', 'desc');

        $episodes = $query->paginate((int) ($validated['per_page'] ?? 15))->appends($request->query());

        return response()->json($episodes);
    }
}
