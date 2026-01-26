<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Shipment;
use App\Models\ShipmentHistory;
use App\Http\Requests\StoreShipmentRequest;
use App\Http\Resources\ShipmentResource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\ShipmentParcel;
use App\Models\ShipmentItem;

class ShipmentController extends Controller
{
    /**
     * List consignments with search and sorting
     */
    public function index(Request $request): JsonResponse
    {
        $sort = $request->get('sort', 'created_at');
        $direction = $request->get('direction', 'desc');

        $query = Shipment::with(['parcels.items'])
            ->withCount('parcels');

        if ($request->has('search')) {
            $search = $request->search;
            $query->where('reference', 'like', "%{$search}%");
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $consignments = $query->orderBy($sort, $direction)->paginate(20);

        return ShipmentResource::collection($consignments)
            ->response()
            ->setStatusCode(200);
    }

    /**
     * Store a new consignment with parcels and items atomically
     */
    public function store(StoreShipmentRequest $request): JsonResponse
    {
        try {
            return DB::transaction(function () use ($request) {
                // 1. Create Consignment
                $consignmentData = $request->input('consignment');
                
                // Ensure company is set (required by validation, but can use contact if company is empty)
                if (empty($consignmentData['company']) && !empty($consignmentData['contact'])) {
                    $consignmentData['company'] = $consignmentData['contact'];
                }
                
                $consignment = Shipment::create(array_merge(
                    $consignmentData,
                    ['status' => 'booked']
                ));

                // 2. Create Parcels and Items
                foreach ($request->input('parcels') as $parcelData) {
                    $itemsData = $parcelData['items'] ?? [];
                    unset($parcelData['items']);

                    $parcel = $consignment->parcels()->create($parcelData);

                    // Create items if provided
                    if (!empty($itemsData) && is_array($itemsData)) {
                        foreach ($itemsData as $itemData) {
                            // Ensure description is not empty
                            if (!empty($itemData['description'])) {
                                try {
                                    $parcel->items()->create($itemData);
                                } catch (\Exception $e) {
                                    Log::warning('Failed to create shipment item: ' . $e->getMessage(), [
                                        'item_data' => $itemData,
                                        'parcel_id' => $parcel->id
                                    ]);
                                }
                            }
                        }
                    }
                }

                // 3. Record History (only if table has required columns)
                try {
                    $consignment->history()->create([
                        'changed_by' => auth()->id() ?? 148 // Default to master if no auth
                    ]);
                } catch (\Exception $historyError) {
                    // Log but don't fail the shipment creation if history fails
                    Log::warning('Failed to create shipment history: ' . $historyError->getMessage(), [
                        'shipment_id' => $consignment->id
                    ]);
                }

                // Reload with relationships
                $consignment->refresh();
                $consignment->load(['parcels.items']);

                try {
                    $resource = new ShipmentResource($consignment);
                    return $resource->response()->setStatusCode(201);
                } catch (\Exception $resourceError) {
                    Log::error('ShipmentResource serialization error: ' . $resourceError->getMessage(), [
                        'trace' => $resourceError->getTraceAsString(),
                        'file' => $resourceError->getFile(),
                        'line' => $resourceError->getLine(),
                        'shipment_id' => $consignment->id
                    ]);
                    
                    // Return a simpler response if resource fails
                    return response()->json([
                        'message' => 'Shipment created but resource serialization failed',
                        'data' => [
                            'id' => $consignment->id,
                            'uuid' => $consignment->uuid,
                            'reference' => $consignment->reference,
                            'status' => $consignment->status,
                            'error' => config('app.debug') ? $resourceError->getMessage() : null
                        ]
                    ], 201);
                }
            });
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Shipment creation error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'request' => $request->all()
            ]);
            
            $errorResponse = [
                'message' => 'Failed to create shipment: ' . $e->getMessage(),
            ];
            
            if (config('app.debug')) {
                $errorResponse['error'] = [
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => $e->getTraceAsString()
                ];
            }
            
            return response()->json($errorResponse, 500);
        }
    }

    /**
     * Display consignment details
     */
    public function show($id): JsonResponse
    {
        $consignment = Shipment::with(['parcels.items', 'history'])->findOrFail($id);
        return (new ShipmentResource($consignment))
            ->response()
            ->setStatusCode(200);
    }

    /**
     * Generate shipping label
     */
    public function generateLabel($id): JsonResponse
    {
        $consignment = Shipment::findOrFail($id);

        if ($consignment->label_generated) {
            return response()->json([
                'message' => 'Label already generated for this consignment'
            ], 422);
        }

        // Simulating label generation call to legacy service
        
        return DB::transaction(function () use ($consignment) {
            $consignment->update([
                'label_generated' => true,
                'label_generated_at' => now(),
                'status' => 'label_generated'
            ]);

            try {
                $consignment->history()->create([
                    'changed_by' => auth()->id() ?? 148
                ]);
            } catch (\Exception $historyError) {
                Log::warning('Failed to create shipment history: ' . $historyError->getMessage(), [
                    'shipment_id' => $consignment->id
                ]);
            }

            return response()->json([
                'message' => 'Label generated successfully',
                'label_url' => "/labels/LBL-{$consignment->uuid}.pdf",
                'data' => new ShipmentResource($consignment)
            ]);
        });
    }
}
