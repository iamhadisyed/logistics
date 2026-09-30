<?php

namespace App\Services\Labels;

use App\Models\Shipment;

/**
 * LabelGeneratorInterface
 *
 * Interface that all carrier label generators must implement.
 * Targets Shipment (the authoritative booking model, per 2026-09-29
 * decision) rather than the deprecated legacy Consignment model.
 */
interface LabelGeneratorInterface
{
    /**
     * Generate label PDF for a shipment
     *
     * @param Shipment $shipment
     * @return string Storage-relative path to the generated PDF file
     */
    public function generateLabel(Shipment $shipment): string;

    /**
     * Get drop-off locations for a postcode
     * 
     * @param string $postcode
     * @param string $countryCode
     * @return array List of drop-off locations
     */
    public function getDropOffLocations(string $postcode, string $countryCode = 'GB'): array;

    /**
     * Get tracking status for a tracking number
     * 
     * @param string $trackingNumber
     * @return array Tracking events
     */
    public function getTrackingStatus(string $trackingNumber): array;
}
