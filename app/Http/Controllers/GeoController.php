<?php

namespace App\Http\Controllers;

use App\Models\City;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GeoController extends Controller
{
    public function citiesByDepartment(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'department_id' => ['required', 'integer', 'exists:departments,id'],
        ]);

        $cities = City::query()
            ->where('department_id', $validated['department_id'])
            ->orderBy('name')
            ->get(['id', 'name']);

        return response()->json($cities);
    }
}
