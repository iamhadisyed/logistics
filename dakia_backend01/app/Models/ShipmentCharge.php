<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShipmentCharge extends Model
{
    protected $table = 'shipment_charges';

    protected $fillable = [
        'shipment_id',
        'charge_type',
        'amount',
        'currency',
        'description',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    // Charge types (same vocabulary as the deprecated ConsignmentCharge)
    const TYPE_BASE_RATE = 'base_rate';
    const TYPE_FUEL_SURCHARGE = 'fuel_surcharge';
    const TYPE_REMOTE_AREA = 'remote_area';
    const TYPE_OVERSIZE = 'oversize';
    const TYPE_INSURANCE = 'insurance';
    const TYPE_COD = 'cod';
    const TYPE_CUSTOMS = 'customs';

    public function shipment(): BelongsTo
    {
        return $this->belongsTo(Shipment::class, 'shipment_id');
    }
}
