<?php

namespace App\Http\Controllers\Logistics;

use App\Http\Controllers\Controller;
use App\Models\Carrier;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class CarrierController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Carrier::with(['country', 'services']);

        // Apply filters
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('country_id')) {
            $query->where('country_id', $request->country_id);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('carrier', 'like', "%{$search}%")
                  ->orWhere('carrier_display_name', 'like', "%{$search}%");
            });
        }

        $carriers = $query->orderBy('carrier_display_name')->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $carriers
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'carrier' => 'required|string|max:45',
                'logo' => 'nullable|string|max:45',
                'cut_off_time' => 'nullable|string|max:45',
                'carrier_display_name' => 'required|string|max:45',
                'status' => 'integer|in:0,1,2',
                'country_id' => 'nullable|exists:countries,id',
                'carrier_id' => 'nullable|integer',
                'currency_code' => 'string|max:3',
                'remotearea_check' => 'in:c,s',
                'zone_base' => 'boolean',
                'zone_type' => 'in:country,postcode',
                'on_contract' => 'boolean',
                'is_gazetteer' => 'boolean',
                'is_reconcile' => 'integer',
            ]);

            $carrier = Carrier::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Carrier created successfully',
                'data' => $carrier->load(['country', 'services'])
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create carrier',
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
            $carrier = Carrier::with(['country', 'services'])->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $carrier
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Carrier not found'
            ], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        try {
            $carrier = Carrier::findOrFail($id);

            $validated = $request->validate([
                'carrier' => 'sometimes|string|max:45',
                'logo' => 'nullable|string|max:45',
                'cut_off_time' => 'nullable|string|max:45',
                'carrier_display_name' => 'sometimes|string|max:45',
                'status' => 'integer|in:0,1,2',
                'country_id' => 'nullable|exists:countries,id',
                'carrier_id' => 'nullable|integer',
                'currency_code' => 'string|max:3',
                'remotearea_check' => 'in:c,s',
                'zone_base' => 'boolean',
                'zone_type' => 'in:country,postcode',
                'on_contract' => 'boolean',
                'is_gazetteer' => 'boolean',
                'is_reconcile' => 'integer',
            ]);

            $carrier->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Carrier updated successfully',
                'data' => $carrier->load(['country', 'services'])
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update carrier',
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
            $carrier = Carrier::findOrFail($id);
            $carrier->delete();

            return response()->json([
                'success' => true,
                'message' => 'Carrier deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete carrier',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
