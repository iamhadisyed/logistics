<?php

namespace App\Http\Controllers\Logistics;

use App\Http\Controllers\Controller;
use App\Models\Consignment;
use App\Models\Service;
use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class ConsignmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Consignment::with(['service', 'country', 'senderCountry', 'user']);

        // Apply filters
        if ($request->has('status')) {
            $query->where('consignment_status', $request->status);
        }

        if ($request->has('service_id')) {
            $query->where('service_id', $request->service_id);
        }

        if ($request->has('country_id')) {
            $query->where('country_id', $request->country_id);
        }

        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('hawb', 'like', "%{$search}%")
                  ->orWhere('awb', 'like', "%{$search}%")
                  ->orWhere('reference', 'like', "%{$search}%")
                  ->orWhere('company', 'like', "%{$search}%")
                  ->orWhere('contact', 'like', "%{$search}%");
            });
        }

        $consignments = $query->orderBy('date_created', 'desc')->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $consignments
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'user_id' => 'nullable|exists:users,id',
                'service_id' => 'required|exists:services,id',
                'hawb' => 'required|string|max:40|unique:consignments,hawb',
                'reference' => 'nullable|string|max:20',
                'company' => 'nullable|string|max:100',
                'contact' => 'nullable|string|max:100',
                'address_line_1' => 'nullable|string|max:50',
                'address_line_2' => 'nullable|string|max:50',
                'address_line_3' => 'nullable|string|max:50',
                'city' => 'nullable|string|max:50',
                'state' => 'nullable|string|max:45',
                'postcode' => 'nullable|string|max:15',
                'country_id' => 'nullable|exists:countries,id',
                'telephone' => 'nullable|string|max:17',
                'email' => 'nullable|email|max:45',
                'weight' => 'nullable|numeric|min:0',
                'value' => 'nullable|numeric|min:0',
                'currency' => 'nullable|string|max:3',
                'description' => 'nullable|string|max:255',
                'notes' => 'nullable|string|max:255',
                'sender_name' => 'required|string|max:45',
                'sender_company' => 'nullable|string|max:100',
                'sender_email' => 'nullable|email|max:45',
                'sender_telephone' => 'nullable|string|max:17',
                'sender_address_line_1' => 'nullable|string|max:255',
                'sender_address_line_2' => 'nullable|string|max:255',
                'sender_address_line_3' => 'nullable|string|max:255',
                'sender_city' => 'nullable|string|max:50',
                'sender_postcode' => 'nullable|string|max:15',
                'sender_country_id' => 'nullable|exists:countries,id',
                'sender_state' => 'nullable|string|max:45',
            ]);

            $consignment = Consignment::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Consignment created successfully',
                'data' => $consignment->load(['service', 'country', 'senderCountry', 'user'])
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
                'message' => 'Failed to create consignment',
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
            $consignment = Consignment::with(['service', 'country', 'senderCountry', 'user'])->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $consignment
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Consignment not found'
            ], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        try {
            $consignment = Consignment::findOrFail($id);

            $validated = $request->validate([
                'service_id' => 'sometimes|exists:services,id',
                'hawb' => 'sometimes|string|max:40|unique:consignments,hawb,' . $id,
                'reference' => 'nullable|string|max:20',
                'company' => 'nullable|string|max:100',
                'contact' => 'nullable|string|max:100',
                'address_line_1' => 'nullable|string|max:50',
                'address_line_2' => 'nullable|string|max:50',
                'address_line_3' => 'nullable|string|max:50',
                'city' => 'nullable|string|max:50',
                'state' => 'nullable|string|max:45',
                'postcode' => 'nullable|string|max:15',
                'country_id' => 'nullable|exists:countries,id',
                'telephone' => 'nullable|string|max:17',
                'email' => 'nullable|email|max:45',
                'weight' => 'nullable|numeric|min:0',
                'value' => 'nullable|numeric|min:0',
                'currency' => 'nullable|string|max:3',
                'description' => 'nullable|string|max:255',
                'notes' => 'nullable|string|max:255',
                'sender_name' => 'sometimes|string|max:45',
                'sender_company' => 'nullable|string|max:100',
                'sender_email' => 'nullable|email|max:45',
                'sender_telephone' => 'nullable|string|max:17',
                'sender_address_line_1' => 'nullable|string|max:255',
                'sender_address_line_2' => 'nullable|string|max:255',
                'sender_address_line_3' => 'nullable|string|max:255',
                'sender_city' => 'nullable|string|max:50',
                'sender_postcode' => 'nullable|string|max:15',
                'sender_country_id' => 'nullable|exists:countries,id',
                'sender_state' => 'nullable|string|max:45',
            ]);

            $consignment->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Consignment updated successfully',
                'data' => $consignment->load(['service', 'country', 'senderCountry', 'user'])
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
                'message' => 'Failed to update consignment',
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
            $consignment = Consignment::findOrFail($id);
            $consignment->delete();

            return response()->json([
                'success' => true,
                'message' => 'Consignment deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete consignment',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get consignment statistics
     */
    public function statistics(): JsonResponse
    {
        try {
            $stats = [
                'total_consignments' => Consignment::count(),
                'pending_consignments' => Consignment::where('consignment_status', 'pending')->count(),
                'in_transit_consignments' => Consignment::where('consignment_status', 'in_transit')->count(),
                'delivered_consignments' => Consignment::where('consignment_status', 'delivered')->count(),
                'total_value' => Consignment::sum('value'),
                'average_weight' => Consignment::avg('weight'),
            ];

            return response()->json([
                'success' => true,
                'data' => $stats
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get statistics',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
