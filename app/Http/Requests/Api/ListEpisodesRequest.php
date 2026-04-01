<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class ListEpisodesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'character_ids' => ['nullable', 'string'],
            'character' => ['nullable', 'string', 'max:255'],
            'season' => ['nullable', 'integer', 'min:1'],
            'air_date_from' => ['nullable', 'date'],
            'air_date_to' => ['nullable', 'date'],
            'sort_by' => ['nullable', 'in:air_date,avg_rating'],
            'sort_dir' => ['nullable', 'in:asc,desc'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
            'page' => ['nullable', 'integer', 'min:1'],
        ];
    }
}
