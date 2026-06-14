<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SearchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'category' => ['required', 'string', 'in:'.implode(',', array_keys(config('travelcash.categories')))],
            'origin' => ['nullable', 'string', 'max:120'],
            'destination' => ['nullable', 'string', 'max:120'],
            'depart_date' => ['nullable', 'date'],
            'return_date' => ['nullable', 'date', 'after_or_equal:depart_date'],
            'travellers' => ['nullable', 'integer', 'min:1', 'max:20'],
            'rooms' => ['nullable', 'integer', 'min:1', 'max:10'],
            'sort' => ['nullable', 'in:lowest_price,highest_cashback,best_value,highest_rating'],
            'filters' => ['nullable', 'array'],
            'limit' => ['nullable', 'integer', 'min:1', 'max:100'],
        ];
    }
}
