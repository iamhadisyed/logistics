<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Carrier extends Model
{
    protected $fillable = [
        'carrier',
        'logo',
        'cut_off_time',
        'carrier_display_name',
        'status',
        'country_id',
        'carrier_id',
        'currency_code',
        'remotearea_check',
        'zone_base',
        'zone_type',
        'on_contract',
        'is_gazetteer',
        'is_reconcile',
    ];

    protected $casts = [
        'status' => 'integer',
        'zone_base' => 'boolean',
        'on_contract' => 'boolean',
        'is_gazetteer' => 'boolean',
        'is_reconcile' => 'integer',
    ];

    // Relationships
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function services(): HasMany
    {
        return $this->hasMany(Service::class);
    }

    public function consignments(): HasMany
    {
        return $this->hasMany(Consignment::class);
    }
}
