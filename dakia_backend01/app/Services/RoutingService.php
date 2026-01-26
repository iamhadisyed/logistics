<?php

namespace App\Services;

use App\Models\CustomizedServicesRouting;
use App\Models\UserServicesRouting;
use App\Models\CarrierServiceCustomizeRules;
use App\Models\CarrierServiceDefaultRules;

/**
 * RoutingService
 * 
 * Implements the 4-step routing algorithm from legacy system:
 * 1. Check user routing
 * 2. Check customized rules
 * 3. Check parent account (recursive)
 * 4. Check default rules
 */
class RoutingService
{
    /**
     * Get agent ID for a given user, service, country, and weight
     * 
     * @param int $userId
     * @param int $serviceId
     * @param int $countryId
     * @param float $weight
     * @return int|null Agent ID or null if not found
     */
    public function getAgentForShipment(int $userId, int $serviceId, int $countryId, float $weight): ?int
    {
        // Step 1: Check user routing
        $userRouting = UserServicesRouting::where('user_account_id', $userId)
            ->where('service_id', $serviceId)
            ->where('country_id', $countryId)
            ->where('from_weight', '<=', $weight)
            ->where('to_weight', '>=', $weight)
            ->where('status', 1)
            ->first();

        if (!$userRouting || !$userRouting->is_agreed) {
            return null; // NON_AGREED
        }

        // Step 2: Check customized rules
        $customRule = CarrierServiceCustomizeRules::where('user_account_id', $userId)
            ->where('serviceid', $serviceId)
            ->where('from_weight', '<=', $weight)
            ->where('to_weight', '>=', $weight)
            ->where('status', 1)
            ->first();

        if ($customRule) {
            return $customRule->agent_id;
        }

        // Step 3: Check parent account (recursive)
        // TODO: Implement parent account check

        // Step 4: Check default rules
        $defaultRule = CarrierServiceDefaultRules::where('serviceid', $serviceId)
            ->where('from_weight', '<=', $weight)
            ->where('to_weight', '>=', $weight)
            ->first();

        if ($defaultRule) {
            return $defaultRule->agent_id;
        }

        return null; // No agent found
    }

    /**
     * Get available services for user based on origin, destination, and weight
     * 
     * @param int $userId
     * @param int $originCountry
     * @param int $destinationCountry
     * @param float $weight
     * @return \Illuminate\Support\Collection
     */
    public function getAvailableServices(int $userId, int $originCountry, int $destinationCountry, float $weight)
    {
        $routings = CustomizedServicesRouting::where('country_id', $destinationCountry)
            ->where('from_weight', '<=', $weight)
            ->where('to_weight', '>=', $weight)
            ->where('status', 'active')
            ->with(['service.carrier'])
            ->get();

        return $routings->map(function($routing) {
            return [
                'service_id' => $routing->service->id,
                'service_name' => $routing->service->name,
                'service_code' => $routing->service->code,
                'carrier_name' => $routing->service->carrier->carrier,
                'carrier_logo' => $routing->service->carrier->logo,
                'volumetric_denominator' => $routing->service->volumetric_denominator,
            ];
        })->unique('service_id')->values();
    }
}
