<?php

namespace App\Http\Controllers\Api;

use App\Models\Consignment;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

class ConsignmentController extends Controller
{
    /**
     * List consignments with filters
     */
    public function index(Request $request): JsonResponse
    {
        $query = Consignment::with(['service.carrier', 'country', 'user']);

        // Apply filters
        if ($request->has('status')) {
            $query->byStatus($request->status);
        }

        if ($request->has('user_id')) {
            $query->byUser($request->user_id);
        }

        if ($request->has('date_from') && $request->has('date_to')) {
            $query->byDateRange($request->date_from, $request->date_to);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('hawb', 'like', "%{$search}%")
                  ->orWhere('awb', 'like', "%{$search}%")
                  ->orWhere('reference', 'like', "%{$search}%");
            });
        }

        $consignments = $query->paginate($request->get('per_page', 20));

        return response()->json($consignments);
    }

    /**
     * Create new consignment (replicate consignment_add.php logic)
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'service_id' => 'required|exists:services,id',
            'company' => 'nullable|max:35',
            'contact' => 'required|max:35',
            'address_line_1' => 'required',
            'city' => 'required',
            'postcode' => 'nullable',
            'country_id' => 'required|exists:country,id',
            'telephone' => 'nullable',
            'email' => 'nullable|email',
            'weight' => 'required|numeric|min:0.001',
            'description' => 'required',
            'number_pieces' => 'required|integer|min:1|max:99',
            'value' => 'nullable|numeric',
        ]);

        $consignment = new Consignment($validated);
        $consignment->user_id = auth()->id();
        $consignment->shipment_status = Consignment::STATUS_NEW;
        $consignment->date_created = now();

        // Validate
        $errors = $consignment->isValid();
        if (!empty($errors)) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $errors
            ], 422);
        }

        // Calculate volumetric weight if parcels provided
        if ($request->has('parcels')) {
            $consignment->save(); // Save first to get ID
            
            foreach ($request->parcels as $parcelData) {
                $consignment->parcels()->create($parcelData);
            }
            
            $consignment->vol_weight = $consignment->calculateVolumetricWeight();
            $consignment->charge_weight = $consignment->getChargeableWeight();
        }

        $consignment->shipment_status = Consignment::STATUS_READY_TO_PRINT;
        $consignment->save();

        return response()->json([
            'message' => 'Consignment created successfully',
            'data' => $consignment->load(['service.carrier', 'parcels'])
        ], 201);
    }

    /**
     * View consignment details
     */
    public function show($id): JsonResponse
    {
        $consignment = Consignment::with([
            'service.carrier',
            'country',
            'senderCountry',
            'parcels',
            'charges',
            'trackingData'
        ])->findOrFail($id);

        return response()->json($consignment);
    }

    /**
     * Update consignment
     */
    public function update(Request $request, $id): JsonResponse
    {
        $consignment = Consignment::findOrFail($id);

        $validated = $request->validate([
            'contact' => 'sometimes|max:35',
            'address_line_1' => 'sometimes',
            'city' => 'sometimes',
            'weight' => 'sometimes|numeric|min:0.001',
            'description' => 'sometimes',
        ]);

        $consignment->update($validated);

        return response()->json([
            'message' => 'Consignment updated successfully',
            'data' => $consignment
        ]);
    }

    /**
     * Delete/recycle consignment
     */
    public function destroy($id): JsonResponse
    {
        $consignment = Consignment::findOrFail($id);
        $consignment->shipment_status = Consignment::STATUS_RECYCLED;
        $consignment->save();

        return response()->json([
            'message' => 'Consignment deleted successfully'
        ]);
    }

    /**
     * Bulk upload consignments from CSV
     */
    public function bulkUpload(Request $request): JsonResponse
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt'
        ]);

        // TODO: Implement CSV parsing and bulk creation
        
        return response()->json([
            'message' => 'Bulk upload feature coming soon'
        ]);
    }

    /**
     * Bulk update consignment statuses
     */
    public function bulkUpdate(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'consignment_ids' => 'required|array',
            'status' => 'required|integer'
        ]);

        Consignment::whereIn('id', $validated['consignment_ids'])
            ->update(['shipment_status' => $validated['status']]);

        return response()->json([
            'message' => 'Consignments updated successfully'
        ]);
    }
}
