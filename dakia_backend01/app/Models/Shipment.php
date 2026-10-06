<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Shipment extends Model
{
    protected $table = 'shipments';

    protected $fillable = [
        'uuid',
        'customer_id',
        'service_type',
        'carrier_id',
        'service_id',
        'warehouse_id',
        'reference',
        'notes',
        'status',
        'label_generated',
        'label_generated_at',
        'label_path',
        // Receiver Address
        'company', 'contact', 'email', 'telephone',
        'address_line_1', 'address_line_2', 'address_line_3',
        'city', 'state', 'postcode', 'country_id',
        // Sender Address
        'sender_company', 'sender_contact', 'sender_email', 'sender_telephone',
        'sender_address_line_1', 'sender_address_line_2', 'sender_address_line_3',
        'sender_city', 'sender_state', 'sender_postcode', 'sender_country_id'
    ];

    protected $casts = [
        'label_generated' => 'boolean',
        'label_generated_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }

    public function parcels(): HasMany
    {
        return $this->hasMany(ShipmentParcel::class, 'shipment_id');
    }

    public function carrier(): BelongsTo
    {
        return $this->belongsTo(Carrier::class, 'carrier_id');
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class, 'service_id');
    }

    public function charges(): HasMany
    {
        return $this->hasMany(ShipmentCharge::class, 'shipment_id');
    }

    /**
     * Total chargeable weight across all parcels: for each parcel, the
     * greater of its actual weight and its volumetric weight
     * (L*W*H / service.volumetric_denominator, legacy default 5000).
     */
    public function getChargeableWeight(): float
    {
        $denominator = $this->service?->volumetric_denominator ?: 5000;

        return (float) $this->parcels->sum(function (ShipmentParcel $parcel) use ($denominator) {
            $actual = (float) $parcel->weight;
            $volumetric = ((float) $parcel->length * (float) $parcel->width * (float) $parcel->height) / $denominator;

            return max($actual, $volumetric);
        });
    }

    public function history(): HasMany
    {
        return $this->hasMany(ShipmentHistory::class, 'shipment_id');
    }
}
