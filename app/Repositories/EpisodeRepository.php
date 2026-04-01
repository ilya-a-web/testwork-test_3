<?php

namespace App\Repositories;

use App\Models\Episode;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class EpisodeRepository
{
    public function paginateWithFilters(array $filters): LengthAwarePaginator
    {
        $query = Episode::query()
            ->with([
                'characters:id,external_id,name,gender,status,url',
                'reviews:id,episode_id,author,text,published_at,rating',
            ])
            ->withAvg('reviews', 'rating');

        if (! empty($filters['season'])) {
            $query->where('season', (int) $filters['season']);
        }

        if (! empty($filters['air_date_from'])) {
            $query->whereDate('air_date', '>=', $filters['air_date_from']);
        }

        if (! empty($filters['air_date_to'])) {
            $query->whereDate('air_date', '<=', $filters['air_date_to']);
        }

        if (! empty($filters['character_ids'])) {
            $ids = collect(explode(',', (string) $filters['character_ids']))
                ->map(fn (string $id): int => (int) trim($id))
                ->filter(fn (int $id): bool => $id > 0)
                ->values();

            if ($ids->isNotEmpty()) {
                $query->whereHas('characters', function (Builder $builder) use ($ids): void {
                    $builder->whereIn('characters.external_id', $ids);
                });
            }
        }

        if (! empty($filters['character'])) {
            $query->whereHas('characters', function (Builder $builder) use ($filters): void {
                $builder->where('name', 'like', '%'.$filters['character'].'%');
            });
        }

        $sortBy = $filters['sort_by'] ?? 'air_date';
        $sortDir = $filters['sort_dir'] ?? 'desc';

        if ($sortBy === 'avg_rating') {
            $query->orderBy('reviews_avg_rating', $sortDir);
        } else {
            $query->orderBy('air_date', $sortDir);
        }

        $query->orderBy('id', 'desc');

        return $query->paginate((int) ($filters['per_page'] ?? 15));
    }

    public function upsert(array $rows): void
    {
        Episode::query()->upsert($rows, ['external_id'], ['name', 'air_date', 'season', 'episode', 'code', 'updated_at']);
    }

    public function mapByExternalId(): Collection
    {
        return Episode::query()->get()->keyBy('external_id');
    }

    public function syncCharacters(Episode $episode, array $characterIds): void
    {
        $episode->characters()->sync(array_values(array_unique($characterIds)));
    }
}
