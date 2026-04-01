<?php

namespace App\Services\Episode;

use App\Repositories\EpisodeRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class EpisodeQueryService
{
    public function __construct(private readonly EpisodeRepository $episodeRepository)
    {
    }

    public function list(array $filters): LengthAwarePaginator
    {
        return $this->episodeRepository->paginateWithFilters($filters);
    }
}
