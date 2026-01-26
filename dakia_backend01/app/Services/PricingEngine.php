<?php

namespace App\Services;

use App\Models\Consignment;
use App\Models\ConsignmentCharge;
use App\Models\Tariff;

/**
 * PricingEngine
 * 
 * Implements pricing calculation from legacy system:
 * 1. Determine chargeable weight
 * 2. Get base rate from tariffs
 * 3. Calculate surcharges
 * 4. Calculate total
 */
class PricingEngine
{
    /**
     * Calculate total price for a consignment
     * 
     * @param Consignment $consignment
     * @return array ['total' => float, 'breakdown' => array]
     */
    public function calculatePrice(Consignment $consignment): array
    {
        $breakdown = [];

        // 1. Determine chargeable weight
        $chargeableWeight = $consignment->getChargeableWeight();

        // 2. Get base rate from tariffs
        $baseRate = $this->getBaseRate($consignment, $chargeableWeight);
        $breakdown[] = [
            'type' => ConsignmentCharge::TYPE_BASE_RATE,
            'amount' => $baseRate,
            'description' => 'Base shipping rate'
        ];

        // 3. Calculate surcharges
        
        // Fuel surcharge
        if ($consignment->service && $consignment->service->fuel_surcharge > 0) {
            $fuelSurcharge = $baseRate * ($consignment->service->fuel_surcharge / 100);
            $breakdown[] = [
                'type' => ConsignmentCharge::TYPE_FUEL_SURCHARGE,
                'amount' => $fuelSurcharge,
                'description' => "Fuel surcharge ({$consignment->service->fuel_surcharge}%)"
            ];
        }

        // Remote area surcharge
        if ($consignment->remote_charges) {
            $remoteAreaCharge = $this->getRemoteAreaCharge($consignment);
            if ($remoteAreaCharge > 0) {
                $breakdown[] = [
                    'type' => ConsignmentCharge::TYPE_REMOTE_AREA,
                    'amount' => $remoteAreaCharge,
                    'description' => 'Remote area surcharge'
                ];
            }
        }

        // Insurance
        if ($consignment->is_insured && $consignment->value > 0) {
            $insuranceRate = 0.02; // 2% of value
            $insuranceFee = $consignment->value * $insuranceRate;
            $breakdown[] = [
                'type' => ConsignmentCharge::TYPE_INSURANCE,
                'amount' => $insuranceFee,
                'description' => 'Insurance fee'
            ];
        }

        // 4. Calculate total
        $total = array_sum(array_column($breakdown, 'amount'));

        return [
            'total' => round($total, 2),
            'breakdown' => $breakdown
        ];
    }

    /**
     * Get base rate from tariffs
     */
    private function getBaseRate(Consignment $consignment, float $weight): float
    {
        // TODO: Implement tariff lookup logic
        // For now, return a placeholder
        return 10.00;
    }

    /**
     * Get remote area charge
     */
    private function getRemoteAreaCharge(Consignment $consignment): float
    {
        // TODO: Implement remote area charge lookup
        return 5.00;
    }

    /**
     * Save charges to database
     */
    public function saveCharges(Consignment $consignment, array $breakdown): void
    {
        // Delete existing charges
        $consignment->charges()->delete();

        // Create new charges
        foreach ($breakdown as $charge) {
            $consignment->charges()->create([
                'charge_type' => $charge['type'],
                'amount' => $charge['amount'],
                'currency' => $consignment->currency ?? 'GBP',
                'description' => $charge['description']
            ]);
        }
    }
}
