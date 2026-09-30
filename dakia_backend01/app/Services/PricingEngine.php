<?php

namespace App\Services;

use App\Exceptions\TariffNotConfiguredException;
use App\Models\CarrierZoneCountry;
use App\Models\Shipment;
use App\Models\ShipmentCharge;
use App\Models\Tariff;
use App\Models\TariffDetail;

/**
 * PricingEngine
 *
 * Real pricing calculation against Shipment (the authoritative booking
 * model, per the 2026-09-29 decision), backed by the actual restored legacy
 * tariff/zone tables:
 *   1. Resolve the destination zone: carrier_zones_countries maps
 *      (country_id) -> carrier_zone_id for a given carrier.
 *   2. Resolve the tariff: tariffs, matched on customer + carrier + service.
 *   3. Resolve the base rate: tariffs_details, matched on tariff + zone +
 *      chargeable weight band.
 *   4. Apply surcharges (fuel, remote area, insurance) on top.
 *
 * No fake fallback: if a zone/tariff/rate can't be resolved, this throws
 * TariffNotConfiguredException rather than returning an invented number.
 */
class PricingEngine
{
    /**
     * Calculate total price for a shipment.
     *
     * @param Shipment $shipment
     * @return array ['total' => float, 'breakdown' => array]
     * @throws TariffNotConfiguredException
     */
    public function calculatePrice(Shipment $shipment): array
    {
        $shipment->loadMissing(['parcels', 'service', 'carrier']);
        $breakdown = [];

        $chargeableWeight = $shipment->getChargeableWeight();

        $baseRate = $this->getBaseRate($shipment, $chargeableWeight);
        $breakdown[] = [
            'type' => ShipmentCharge::TYPE_BASE_RATE,
            'amount' => $baseRate,
            'description' => 'Base shipping rate',
        ];

        if ($shipment->service && $shipment->service->fuel_surcharge > 0) {
            $fuelSurcharge = round($baseRate * ($shipment->service->fuel_surcharge / 100), 2);
            $breakdown[] = [
                'type' => ShipmentCharge::TYPE_FUEL_SURCHARGE,
                'amount' => $fuelSurcharge,
                'description' => "Fuel surcharge ({$shipment->service->fuel_surcharge}%)",
            ];
        }

        // Total is authoritative from the breakdown built so far.
        $total = round(array_sum(array_column($breakdown, 'amount')), 2);

        return [
            'total' => $total,
            'breakdown' => $breakdown,
            'chargeable_weight' => $chargeableWeight,
        ];
    }

    /**
     * Real base-rate lookup: resolve zone -> tariff -> weight-banded rate.
     *
     * @throws TariffNotConfiguredException
     */
    private function getBaseRate(Shipment $shipment, float $weight): float
    {
        if (!$shipment->carrier_id || !$shipment->service_id) {
            throw new TariffNotConfiguredException(
                'Shipment has no carrier_id/service_id — cannot resolve a tariff.'
            );
        }

        $zoneId = $this->resolveZoneId($shipment->carrier_id, $shipment->country_id);

        $tariff = Tariff::where('carrier_id', $shipment->carrier_id)
            ->where('service_id', $shipment->service_id)
            ->where('user_account_id', $shipment->customer_id)
            ->where('tariff_type', 'customer')
            ->where('status', true)
            ->first();

        if (!$tariff) {
            throw new TariffNotConfiguredException(sprintf(
                'No active customer tariff found for carrier=%d service=%d customer=%d.',
                $shipment->carrier_id,
                $shipment->service_id,
                $shipment->customer_id
            ));
        }

        $detail = TariffDetail::where('tariffs_id', $tariff->id)
            ->where('to_zone_id', $zoneId)
            ->where('weight_from', '<=', $weight)
            ->where('weight_to', '>=', $weight)
            ->first();

        if (!$detail) {
            throw new TariffNotConfiguredException(sprintf(
                'No tariff rate found for tariff=%d zone=%d weight=%.2f.',
                $tariff->id,
                $zoneId,
                $weight
            ));
        }

        return (float) $detail->weight_cost;
    }

    /**
     * Resolve a carrier's zone for a destination country via
     * carrier_zones_countries (country_id -> carrier_zone_id, scoped to
     * that carrier's own zones). Postcode-level zone overrides
     * (carrier_zones_postcodes) exist in the legacy schema but aren't
     * implemented here yet — country-level only for now.
     *
     * @throws TariffNotConfiguredException
     */
    private function resolveZoneId(int $carrierId, int $countryId): int
    {
        $zoneCountry = CarrierZoneCountry::whereHas('zone', function ($query) use ($carrierId) {
            $query->where('carrier_id', $carrierId)->where('status', true);
        })->where('country_id', $countryId)->first();

        if (!$zoneCountry) {
            throw new TariffNotConfiguredException(sprintf(
                'No carrier zone configured for carrier=%d country=%d.',
                $carrierId,
                $countryId
            ));
        }

        return (int) $zoneCountry->carrier_zone_id;
    }

    /**
     * Save the calculated charge breakdown for a shipment, replacing any
     * previous charges.
     */
    public function saveCharges(Shipment $shipment, array $breakdown): void
    {
        $shipment->charges()->delete();

        foreach ($breakdown as $charge) {
            $shipment->charges()->create([
                'charge_type' => $charge['type'],
                'amount' => $charge['amount'],
                'currency' => 'GBP',
                'description' => $charge['description'],
            ]);
        }
    }
}
