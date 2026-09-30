<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CarrierZoneCountry extends Model
{
    protected $table = 'carrier_zones_countries';

    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id',
        'country_id',
        'carrier_zone_id',
    ];

    public function zone(): BelongsTo
    {
        return $this->belongsTo(CarrierZone::class, 'carrier_zone_id');
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'country_id');
    }
}
