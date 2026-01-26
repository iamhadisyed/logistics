<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ShipmentParcel extends Model
{
    protected $table = 'shipment_parcels';

    protected $fillable = [
        'shipment_id',
        'weight',
        'length',
        'width',
        'height',
        'notes'
    ];

    protected $casts = [
        'weight' => 'decimal:2',
        'length' => 'decimal:2',
        'width' => 'decimal:2',
        'height' => 'decimal:2',
    ];

    public function shipment(): BelongsTo
    {
        return $this->belongsTo(Shipment::class, 'shipment_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(ShipmentItem::class, 'parcel_id');
    }
}
