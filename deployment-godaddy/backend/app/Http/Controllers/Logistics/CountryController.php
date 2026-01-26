<?php

namespace App\Http\Controllers\Logistics;

use App\Http\Controllers\Controller;
use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CountryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Country::query();

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('iso', 'like', "%{$search}%")
                  ->orWhere('printable_name', 'like', "%{$search}%");
            });
        }

        $countries = $query->orderBy('name')->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $countries
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'iso' => 'required|string|max:3|unique:countries,iso',
                'name' => 'required|string|max:80',
                'printable_name' => 'required|string|max:80',
                'region' => 'nullable|string|max:45',
                'postcode_required' => 'in:YES,NO',
                'iso3' => 'nullable|string|max:3',
            ]);

            $country = Country::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Country created successfully',
                'data' => $country
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create country',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        try {
            $country = Country::findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $country
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Country not found'
            ], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        try {
            $country = Country::findOrFail($id);

            $validated = $request->validate([
                'iso' => 'sometimes|string|max:3|unique:countries,iso,' . $id,
                'name' => 'sometimes|string|max:80',
                'printable_name' => 'sometimes|string|max:80',
                'region' => 'nullable|string|max:45',
                'postcode_required' => 'in:YES,NO',
                'iso3' => 'nullable|string|max:3',
            ]);

            $country->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Country updated successfully',
                'data' => $country
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update country',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $country = Country::findOrFail($id);
            $country->delete();

            return response()->json([
                'success' => true,
                'message' => 'Country deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete country',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
