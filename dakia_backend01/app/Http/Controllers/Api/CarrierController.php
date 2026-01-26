<?php

namespace App\Http\Controllers\Api;

use App\Models\Carrier;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

class CarrierController extends Controller
{
    /**
     * List carriers
     */
    public function index(Request $request): JsonResponse
    {
        $query = Carrier::with(['services', 'country'])->notDeleted();

        if ($request->has('active_only') && $request->active_only) {
            $query->active();
        }

        $carriers = $query->get();

        return response()->json($carriers);
    }

    /**
     * Get carrier details with services
     */
    public function show($id): JsonResponse
    {
        $carrier = Carrier::with(['services' => function($query) {
            $query->active();
        }])->findOrFail($id);

        return response()->json($carrier);
    }

    /**
     * Create carrier (admin)
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'carrier' => 'required|string|max:255',
            'carrier_display_name' => 'nullable|string|max:255',
            'logo' => 'nullable|string',
            'carrier_id' => 'nullable|exists:carriers,id', // Parent carrier
            'country_id' => 'nullable|exists:country,id',
            'currency_code' => 'nullable|string|max:3',
            'cut_off_time' => 'nullable|string|max:10',
            'zone_base' => 'nullable|boolean',
            'zone_type' => 'nullable|in:country,postcode',
            'remotearea_check' => 'nullable|in:c,s',
            'is_gazetteer' => 'nullable|boolean',
            'is_reconcile' => 'nullable|boolean',
            'on_contract' => 'nullable|boolean',
            'is_pallet' => 'nullable|boolean',
        ]);

        $carrier = Carrier::create(array_merge($validated, [
            'status' => Carrier::STATUS_ACTIVE
        ]));

        return response()->json([
            'message' => 'Carrier created successfully',
            'data' => $carrier
        ], 201);
    }

    /**
     * Update carrier (admin)
     */
    public function update(Request $request, $id): JsonResponse
    {
        $carrier = Carrier::findOrFail($id);

        $validated = $request->validate([
            'carrier' => 'sometimes|string|max:255',
            'carrier_display_name' => 'sometimes|string|max:255',
            'logo' => 'sometimes|string',
            'carrier_id' => 'sometimes|nullable|exists:carriers,id', // Parent carrier
            'country_id' => 'sometimes|nullable|exists:country,id',
            'currency_code' => 'sometimes|nullable|string|max:3',
            'cut_off_time' => 'sometimes|nullable|string|max:10',
            'zone_base' => 'sometimes|nullable|boolean',
            'zone_type' => 'sometimes|nullable|in:country,postcode',
            'remotearea_check' => 'sometimes|nullable|in:c,s',
            'is_gazetteer' => 'sometimes|nullable|boolean',
            'is_reconcile' => 'sometimes|nullable|boolean',
            'on_contract' => 'sometimes|nullable|boolean',
            'is_pallet' => 'sometimes|nullable|boolean',
        ]);

        $carrier->update($validated);

        return response()->json([
            'message' => 'Carrier updated successfully',
            'data' => $carrier
        ]);
    }

    /**
     * Activate/deactivate carrier
     * Implements cascade logic from legacy
     */
    public function updateStatus(Request $request, $id): JsonResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:active,inactive,delete'
        ]);

        $carrier = Carrier::findOrFail($id);

        switch ($validated['status']) {
            case 'active':
                $carrier->activate();
                $message = 'Carrier activated successfully';
                break;
            case 'inactive':
                $carrier->deactivate(); // This cascades to services
                $message = 'Carrier and all its services deactivated successfully';
                break;
            case 'delete':
                $carrier->status = Carrier::STATUS_DELETED;
                $carrier->save();
                $message = 'Carrier deleted successfully';
                break;
        }

        return response()->json([
            'message' => $message,
            'data' => $carrier
        ]);
    }
}
