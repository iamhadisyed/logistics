<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\Carrier;
use App\Models\User;
use App\Models\UserAccount;
use App\Models\UserServicesRouting;
use App\Models\CarrierServiceCustomizeRules;
use App\Models\CarrierServiceDefaultRules;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class ServiceController extends Controller
{
    /**
     * Defensive check to ensure the user belongs to the master account (ID 148).
     */
    private function checkMasterAccount(Request $request)
    {
        $user = $request->user();
        if (!$user || $user->user_account_id != 148) {
            abort(403, 'Access Denied: Master Account only.');
        }
    }

    /**
     * List all services (Admin)
     */
    public function index(Request $request): JsonResponse
    {
        $this->checkMasterAccount($request);
        
        $query = Service::with('carrier');

        if ($request->has('active_only') && $request->active_only) {
            $query->active();
        }

        return response()->json($query->get());
    }

    /**
     * Create a new service
     */
    public function store(Request $request): JsonResponse
    {
        $this->checkMasterAccount($request);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|unique:services,code',
            'carrier_id' => 'required|exists:carriers,id',
            'type' => 'required|in:D,I,E,R',
            'from_weight' => 'required|numeric',
            'to_weight' => 'required|numeric',
            'active' => 'boolean',
        ]);

        // Auto-increment ID manually if needed (legacy table support)
        $id = Service::max('id') + 1;
        
        $service = Service::create(array_merge($validated, [
            'id' => $id,
            'active' => $request->boolean('active', true)
        ]));

        return response()->json([
            'message' => 'Service created successfully',
            'data' => $service
        ], 201);
    }

    /**
     * Show service details
     */
    public function show(Request $request, $id): JsonResponse
    {
        $this->checkMasterAccount($request);
        $service = Service::with('carrier')->findOrFail($id);
        return response()->json($service);
    }

    /**
     * Update service
     */
    public function update(Request $request, $id): JsonResponse
    {
        $this->checkMasterAccount($request);
        $service = Service::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'code' => 'sometimes|string|unique:services,code,' . $id,
            'carrier_id' => 'sometimes|exists:carriers,id',
            'type' => 'sometimes|in:D,I,E,R',
            'from_weight' => 'sometimes|numeric',
            'to_weight' => 'sometimes|numeric',
            'active' => 'boolean',
        ]);

        $service->update($validated);

        return response()->json([
            'message' => 'Service updated successfully',
            'data' => $service
        ]);
    }

    /**
     * Delete service (Soft or Hard?)
     * Legacy implies soft delete by setting active=0 or status=2
     * Service model has 'active' (boolean).
     */
    public function destroy(Request $request, $id): JsonResponse
    {
        $this->checkMasterAccount($request);
        $service = Service::findOrFail($id);
        
        // Soft delete logic: set active = 0
        $service->active = 0;
        $service->save();

        return response()->json(['message' => 'Service deactivated successfully']);
    }

    /**
     * List all services linked to an account (only active ones).
     */
    public function getAccountServices(Request $request, $accountId): JsonResponse
    {
        $this->checkMasterAccount($request);
        
        $routing = UserServicesRouting::where('user_account_id', $accountId)
            ->where('status', 1)
            ->with('service')
            ->get();

        $services = $routing->map(function($r) {
            return $r->service;
        })->filter();

        return response()->json($services->values());
    }

    /**
     * Assign one or more services to an account.
     */
    public function assignAccountServices(Request $request, $accountId): JsonResponse
    {
        $this->checkMasterAccount($request);

        $validator = Validator::make($request->all(), [
            'service_ids' => 'required|array',
            'service_ids.*' => 'exists:services,id'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        foreach ($request->service_ids as $serviceId) {
            $exists = UserServicesRouting::where('user_account_id', $accountId)
                ->where('service_id', $serviceId)
                ->exists();

            if (!$exists) {
                $nextId = UserServicesRouting::max('id') + 1;
                UserServicesRouting::create([
                    'id' => $nextId,
                    'user_account_id' => $accountId,
                    'service_id' => $serviceId,
                    'status' => 1,
                    // Default values for legacy columns without defaults
                    'country_id' => 0,
                    'from_weight' => 0,
                    'to_weight' => 9999,
                    'is_remotearea' => 0,
                    'is_over_label' => 0,
                    'added_by' => $request->user()->id,
                    'is_agreed' => 0,
                    'label_charges' => 0,
                    'is_dead_weight' => 0,
                    'is_over_size' => 0,
                ]);
            } else {
                // Reactivate if exists but inactive
                UserServicesRouting::where('user_account_id', $accountId)
                    ->where('service_id', $serviceId)
                    ->update(['status' => 1]);
            }
        }

        return response()->json(['message' => 'Services assigned successfully']);
    }

    /**
     * Remove service assignment (simulate soft delete).
     */
    public function removeAccountService(Request $request, $accountId, $serviceId): JsonResponse
    {
        $this->checkMasterAccount($request);

        UserServicesRouting::where('user_account_id', $accountId)
            ->where('service_id', $serviceId)
            ->update(['status' => 0]);

        return response()->json(['message' => 'Service assignment removed successfully']);
    }

    /**
     * List services available to a user (inherited from account).
     */
    public function getUserServices(Request $request, $userId): JsonResponse
    {
        $this->checkMasterAccount($request);
        
        $user = User::find($userId);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        return $this->getAccountServices($request, $user->user_account_id);
    }

    /**
     * Assign specific services to a user (optional override - actually account-level as per schema).
     */
    public function assignUserServices(Request $request, $userId): JsonResponse
    {
        $this->checkMasterAccount($request);
        
        $user = User::find($userId);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        return $this->assignAccountServices($request, $user->user_account_id);
    }

    /**
     * Show routing rules for a service.
     */
    public function getServiceRouting(Request $request, $serviceId): JsonResponse
    {
        $this->checkMasterAccount($request);

        $rules = CarrierServiceCustomizeRules::where('serviceid', $serviceId)
            ->where('status', 1)
            ->get();

        return response()->json($rules);
    }

    /**
     * Add/update routing rules.
     */
    public function updateServiceRouting(Request $request, $serviceId): JsonResponse
    {
        $this->checkMasterAccount($request);

        $validator = Validator::make($request->all(), [
            'agentid' => 'required|exists:carriers,id',
            'user_account_id' => 'required|exists:user_accounts,id',
            'from_weight' => 'required|numeric',
            'to_weight' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $nextId = CarrierServiceCustomizeRules::max('id') + 1;
        CarrierServiceCustomizeRules::create([
            'id' => $nextId,
            'serviceid' => $serviceId,
            'agentid' => $request->agentid,
            'user_account_id' => $request->user_account_id,
            'from_weight' => $request->from_weight,
            'to_weight' => $request->to_weight,
            'status' => 1
        ]);

        return response()->json(['message' => 'Routing rule added successfully']);
    }

    /**
     * Remove routing rule (simulate soft delete).
     */
    public function removeServiceRouting(Request $request, $serviceId, $ruleId): JsonResponse
    {
        $this->checkMasterAccount($request);

        CarrierServiceCustomizeRules::where('serviceid', $serviceId)
            ->where('id', $ruleId)
            ->update(['status' => 0]);

        return response()->json(['message' => 'Routing rule removed successfully']);
    }

    /**
     * List all active services.
     */
    public function getAllServices(Request $request): JsonResponse
    {
        $this->checkMasterAccount($request);
        return response()->json(Service::where('active', 1)->get());
    }

    /**
     * List all active carriers.
     */
    public function getAllCarriers(Request $request): JsonResponse
    {
        $this->checkMasterAccount($request);
        return response()->json(Carrier::where('status', 1)->get());
    }
}
