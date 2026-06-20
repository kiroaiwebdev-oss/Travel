<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * City autocomplete. Searches the bundled cities list server-side so the full
 * dataset is never shipped to the browser. Swap config('cities.list') for a DB
 * table or geocoding API here to get exhaustive worldwide coverage with no
 * frontend changes.
 */
class CityController extends Controller
{
    public function search(Request $request): JsonResponse
    {
        $q = trim((string) $request->query('q', ''));
        $all = (array) config('cities.list', []);

        if ($q === '') {
            return response()->json(array_values(array_slice($all, 0, 10)));
        }

        $needle = mb_strtolower($q);
        $starts = [];
        $contains = [];
        foreach ($all as $city) {
            $hay = mb_strtolower($city);
            if (str_starts_with($hay, $needle)) {
                $starts[] = $city;
            } elseif (str_contains($hay, $needle)) {
                $contains[] = $city;
            }
        }

        return response()->json(array_values(array_slice(array_merge($starts, $contains), 0, 10)));
    }
}
