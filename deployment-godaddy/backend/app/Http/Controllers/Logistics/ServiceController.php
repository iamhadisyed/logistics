<?php

namespace App\Http\Controllers\Logistics;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Service::with(['carrier']);

        if ($request->has('carrier_id')) {
            $query->where('carrier_id', $request->carrier_id);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        $services = $query->orderBy('name')->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $services
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:45',
                'code' => 'required|string|max:20',
                'carrier_id' => 'required|exists:carriers,id',
                'service_type' => 'required|in:D,C,B,DO',
                'description' => 'nullable|string',
                'fuel_surcharge' => 'required|numeric',
                'fuel_surcharge_type' => 'required|string|max:1',
                'max_length' => 'required|numeric',
                'max_width' => 'required|numeric',
                'max_height' => 'required|numeric',
            ]);

            $service = Service::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Service created successfully',
                'data' => $service->load(['carrier'])
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create service',
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
            $service = Service::with(['carrier'])->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $service
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Service not found'
            ], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        try {
            $service = Service::findOrFail($id);

            $validated = $request->validate([
                'name' => 'sometimes|string|max:45',
                'code' => 'sometimes|string|max:20',
                'carrier_id' => 'sometimes|exists:carriers,id',
                'service_type' => 'sometimes|in:D,C,B,DO',
                'description' => 'nullable|string',
                'fuel_surcharge' => 'sometimes|numeric',
                'fuel_surcharge_type' => 'sometimes|string|max:1',
                'max_length' => 'sometimes|numeric',
                'max_width' => 'sometimes|numeric',
                'max_height' => 'sometimes|numeric',
            ]);

            $service->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Service updated successfully',
                'data' => $service->load(['carrier'])
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update service',
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
            $service = Service::findOrFail($id);
            $service->delete();

            return response()->json([
                'success' => true,
                'message' => 'Service deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete service',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
