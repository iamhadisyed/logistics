<?php

namespace App\Services\Labels;

use App\Models\Shipment;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

/**
 * DefaultLabelGenerator
 *
 * Renders a real PDF shipping label from Shipment data (address, parcels,
 * reference) and stores it. This is a carrier-agnostic fallback — it does
 * NOT call any real carrier API (DHL/UPS/etc.), because no carrier
 * credentials exist in this project yet. Per-carrier generators (matching
 * the legacy carrier integrations in logistic/main/) should implement this
 * same interface and be selected by carrier/service instead of this class,
 * once carrier API credentials are available.
 */
class DefaultLabelGenerator implements LabelGeneratorInterface
{
    public function generateLabel(Shipment $shipment): string
    {
        $shipment->loadMissing('parcels.items');

        $countryName = $shipment->country_id
            ? (\App\Models\Country::find($shipment->country_id)?->name ?? 'Unknown')
            : 'Unknown';

        $pdf = Pdf::loadView('labels.shipment', [
            'shipment' => $shipment,
            'countryName' => $countryName,
            'generatedAt' => now()->format('Y-m-d H:i'),
        ])->setPaper([0, 0, 288, 432]); // ~4x6 inch label

        $relativePath = "labels/LBL-{$shipment->uuid}.pdf";
        Storage::disk('local')->put($relativePath, $pdf->output());

        return $relativePath;
    }

    public function getDropOffLocations(string $postcode, string $countryCode = 'GB'): array
    {
        // Not implemented: needs a real carrier drop-off-location API
        // (legacy: dropoff_service.php). No carrier credentials configured.
        throw new RuntimeException('Drop-off location lookup is not implemented yet.');
    }

    public function getTrackingStatus(string $trackingNumber): array
    {
        // Not implemented: needs a real carrier tracking API
        // (legacy: tracking.php, multitracking.php). No carrier credentials
        // configured — see MODULE_COMPLETION_TRACKER.md module 19.
        throw new RuntimeException('Tracking status lookup is not implemented yet.');
    }
}
