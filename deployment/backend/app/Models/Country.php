<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Country extends Model
{
    protected $fillable = [
        'iso',
        'name',
        'region',
        'postcode_required',
        'type',
        'region_collection',
        'numcode',
        'allow_express',
        'allow_classic',
        'eu_country',
        'shipping_advice',
        'is_vatable',
        'vat_rate',
        'printable_name',
        'iso3',
        'export_flag',
        'timezone_difference',
        'has_postcodeq',
        'has_subzonesq',
        'orderq',
        'active',
        'deletedq',
        'added_on',
        'added_by',
        'changed_on',
        'changed_by',
        'vat_charged_flag',
        'customs_flag',
        'description',
    ];

    protected $casts = [
        'postcode_required' => 'string',
        'is_vatable' => 'string',
        'vat_rate' => 'decimal:2',
        'added_on' => 'datetime',
        'changed_on' => 'datetime',
        'active' => 'boolean',
    ];

    // Relationships
    public function carriers(): HasMany
    {
        return $this->hasMany(Carrier::class);
    }

    public function consignments(): HasMany
    {
        return $this->hasMany(Consignment::class);
    }

    public function senderConsignments(): HasMany
    {
        return $this->hasMany(Consignment::class, 'sender_country_id');
    }
}
