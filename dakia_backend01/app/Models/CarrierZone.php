<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CarrierZone extends Model
{
    protected $table = 'carrier_zones';

    // Plain integer id, expected from legacy data import (same pattern as
    // Tariff/TariffDetail).
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id',
        'carrier_id',
        'service_id',
        'name',
        'sort_order',
        'status',
        'deleted',
    ];

    protected $casts = [
        'status' => 'boolean',
        'deleted' => 'boolean',
    ];

    public function countries(): HasMany
    {
        return $this->hasMany(CarrierZoneCountry::class, 'carrier_zone_id');
    }
}
