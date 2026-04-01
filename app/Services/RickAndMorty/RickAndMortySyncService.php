<?php

namespace App\Services\RickAndMorty;

use App\Repositories\CharacterRepository;
use App\Repositories\EpisodeRepository;
use Illuminate\Support\Carbon;

class RickAndMortySyncService
{
    public function __construct(
        private readonly RickAndMortyApiClient $apiClient,
        private readonly ReviewSeederService $reviewSeeder,
        private readonly CharacterRepository $characterRepository,
        private readonly EpisodeRepository $episodeRepository,
    ) {
    }

    public function sync(): array
    {
        $characters = $this->apiClient->fetchAllCharacters();
        $episodes = $this->apiClient->fetchAllEpisodes();

        $this->syncCharacters($characters);
        $this->syncEpisodes($episodes);

        $seededReviews = $this->syncRelationsAndSeedReviews($episodes);

        return [
            'characters' => count($characters),
            'episodes' => count($episodes),
            'seeded_reviews' => $seededReviews,
        ];
    }

    private function syncCharacters(array $characters): void
    {
        $rows = array_map(function (array $character): array {
            return [
                'external_id' => (int) $character['id'],
                'name' => (string) $character['name'],
                'gender' => (string) ($character['gender'] ?? ''),
                'status' => $this->normalizeStatus((string) ($character['status'] ?? 'unknown')),
                'url' => (string) $character['url'],
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }, $characters);

        $this->characterRepository->upsert($rows);
    }

    private function syncEpisodes(array $episodes): void
    {
        $rows = array_map(function (array $episode): array {
            [$season, $episodeNumber] = $this->parseEpisodeCode((string) $episode['episode']);

            return [
                'external_id' => (int) $episode['id'],
                'name' => (string) $episode['name'],
                'air_date' => $this->parseAirDate($episode['air_date'] ?? null),
                'season' => $season,
                'episode' => $episodeNumber,
                'code' => (string) $episode['episode'],
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }, $episodes);

        $this->episodeRepository->upsert($rows);
    }

    private function syncRelationsAndSeedReviews(array $episodes): int
    {
        $characterByUrl = $this->characterRepository->idByUrlMap();
        $episodesByExternalId = $this->episodeRepository->mapByExternalId();

        $seeded = 0;

        foreach ($episodes as $episodeRaw) {
            $episode = $episodesByExternalId->get((int) $episodeRaw['id']);
            if (! $episode) {
                continue;
            }

            $characterIds = [];
            foreach (($episodeRaw['characters'] ?? []) as $characterUrl) {
                if (isset($characterByUrl[$characterUrl])) {
                    $characterIds[] = $characterByUrl[$characterUrl];
                }
            }

            $this->episodeRepository->syncCharacters($episode, $characterIds);
            $seeded += $this->reviewSeeder->seedForEpisode($episode);
        }

        return $seeded;
    }

    private function normalizeStatus(string $status): string
    {
        return match (strtolower($status)) {
            'alive' => 'жив',
            'dead' => 'мёртв',
            default => 'неизвестно',
        };
    }

    private function parseEpisodeCode(string $code): array
    {
        if (preg_match('/S(\d+)E(\d+)/i', $code, $matches) === 1) {
            return [(int) $matches[1], (int) $matches[2]];
        }

        return [0, 0];
    }

    private function parseAirDate(null|string $date): ?string
    {
        if (! $date) {
            return null;
        }

        try {
            return Carbon::parse($date)->toDateString();
        } catch (\Throwable) {
            return null;
        }
    }
}
