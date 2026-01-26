<?php

namespace App\Services\Labels;

use App\Models\Consignment;

/**
 * LabelGeneratorInterface
 * 
 * Interface that all carrier label generators must implement
 */
interface LabelGeneratorInterface
{
    /**
     * Generate label PDF for a consignment
     * 
     * @param Consignment $consignment
     * @return string Path to generated PDF file
     */
    public function generateLabel(Consignment $consignment): string;

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
