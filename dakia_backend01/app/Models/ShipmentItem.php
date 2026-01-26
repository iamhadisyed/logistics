<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShipmentItem extends Model
{
    protected $table = 'shipment_items';

    protected $fillable = [
        'parcel_id',
        'description',
        'quantity',
        'weight',
        'value'
    ];

    protected $casts = [
        'weight' => 'decimal:2',
        'value' => 'decimal:2',
    ];

    public function parcel(): BelongsTo
    {
        return $this->belongsTo(ShipmentParcel::class, 'parcel_id');
    }
}
