<?php

namespace App\Services\RickAndMorty;

use Illuminate\Support\Facades\Http;

class RickAndMortyApiClient
{
    public function fetchAllCharacters(): array
    {
        return $this->fetchAll('/character');
    }

    public function fetchAllEpisodes(): array
    {
        return $this->fetchAll('/episode');
    }

    private function fetchAll(string $path): array
    {
        $items = [];
        $next = rtrim((string) config('rickmorty.base_url'), '/').$path;
        $rateLimitAttempts = 0;

        while ($next) {
            $response = Http::acceptJson()
                ->timeout(20)
                ->withHeaders([
                    'User-Agent' => 'cable-rf-test-task/1.0',
                ])
                ->get($next);

            if ($response->status() === 429) {
                $rateLimitAttempts++;
                if ($rateLimitAttempts > 5) {
                    break;
                }
                sleep(3);
                continue;
            }
            $rateLimitAttempts = 0;

            $response = $response->throw()->json();

            $items = [...$items, ...($response['results'] ?? [])];
            $next = $response['info']['next'] ?? null;
        }

        return $items;
    }
}
