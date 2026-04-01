<?php

return [
    'base_url' => env('RICKMORTY_BASE_URL', 'https://rickandmortyapi.com/api'),
    'reviews_json_path' => env('REVIEWS_JSON_PATH', 'reviews.json'),
    'reviews_seed_min' => (int) env('REVIEWS_SEED_MIN', 50),
    'reviews_seed_max' => (int) env('REVIEWS_SEED_MAX', 500),
];
