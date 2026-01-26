<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Service extends Model
{
    protected $fillable = [
        'name',
        'code',
        'carrier_id',
        'account_number',
        'type',
        'from_weight',
        'to_weight',
        'weight_type',
        'supplier',
        'service_type',
        'drop_off_service_id',
        'description',
        'fuel_surcharge_cost',
        'fuel_surcharge',
        'fuel_surcharge_type',
        'max_length',
        'max_width',
        'max_height',
        'max_volumetric_weight',
        'volumetric_denominator',
        'send_data_courier',
        'is_document',
        'friday_only_flag',
        'saturday_only_flag',
        'sunday_only_flag',
        'product_owner',
        'active',
        'deletedq',
        'added_on',
        'added_by',
        'changed_on',
        'changed_by',
        'uploaded_currency',
        'uploaded_currency_value',
        'registration_fee',
        'weight_after',
        'additional_charge',
        'origin_country',
        'is_untrack',
        'account_owner',
        'remotearea',
        'carrier_address_limit',
        'label_class_name',
        'transit_time',
        'required_email',
        'required_telephone',
        'shipment_type',
        'pre_sort',
        'proforma_invoice',
    ];

    protected $casts = [
        'from_weight' => 'decimal:3',
        'to_weight' => 'decimal:3',
        'fuel_surcharge_cost' => 'decimal:2',
        'fuel_surcharge' => 'decimal:2',
        'max_length' => 'decimal:2',
        'max_width' => 'decimal:2',
        'max_height' => 'decimal:2',
        'max_volumetric_weight' => 'decimal:2',
        'uploaded_currency_value' => 'decimal:2',
        'registration_fee' => 'decimal:2',
        'weight_after' => 'decimal:2',
        'additional_charge' => 'decimal:2',
        'send_data_courier' => 'integer',
        'is_document' => 'boolean',
        'friday_only_flag' => 'boolean',
        'saturday_only_flag' => 'boolean',
        'sunday_only_flag' => 'boolean',
        'active' => 'boolean',
        'deletedq' => 'boolean',
        'is_untrack' => 'boolean',
        'required_email' => 'boolean',
        'required_telephone' => 'boolean',
        'proforma_invoice' => 'boolean',
        'added_on' => 'datetime',
        'changed_on' => 'datetime',
    ];

    // Relationships
    public function carrier(): BelongsTo
    {
        return $this->belongsTo(Carrier::class);
    }

    public function consignments(): HasMany
    {
        return $this->hasMany(Consignment::class);
    }
}
