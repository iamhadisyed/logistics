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
        'warehouse_id',
        'reference',
        'notes',
        'status',
        'label_generated',
        'label_generated_at',
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

    public function history(): HasMany
    {
        return $this->hasMany(ShipmentHistory::class, 'shipment_id');
    }
}
