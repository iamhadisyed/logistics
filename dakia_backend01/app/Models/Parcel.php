<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Parcel extends Model
{
    
    protected $fillable = [
        'consignment_id',
        'tracking_number',
        'length',
        'width',
        'height',
        'weight',
        'barcode_data',
    ];

    protected $casts = [
        'length' => 'decimal:2',
        'width' => 'decimal:2',
        'height' => 'decimal:2',
        'weight' => 'decimal:3',
    ];

    public function consignment(): BelongsTo
    {
        return $this->belongsTo(Consignment::class);
    }

    // Calculate volumetric weight for this parcel
    public function calculateVolumetricWeight($denominator = 5000): float
    {
        return ($this->length * $this->width * $this->height) / $denominator;
    }
}
