<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Consignment extends Model
{
    public $timestamps = false; // Legacy generally handles create/update dates manually
    
    protected $fillable = [
        // Core fields
        'user_id',
        'service_id',
        'warehouse_id',
        'agent_id',
        'awb',
        'hawb',
        'shipment_status',
        'shipment_type',
        'reference',
        'date_created',
        'date_label_created',
        
        // Receiver address
        'company',
        'contact',
        'address_line_1',
        'address_line_2',
        'address_line_3',
        'city',
        'state',
        'postcode',
        'country_id',
        'telephone',
        'email',
        
        // Sender address
        'sender_company',
        'sender_contact',
        'sender_address_line_1',
        'sender_address_line_2',
        'sender_address_line_3',
        'sender_city',
        'sender_state',
        'sender_postcode',
        'sender_country_id',
        'sender_telephone',
        'sender_email',
        
        // Parcel details
        'number_pieces',
        'weight',
        'vol_weight',
        'charge_weight',
        'description',
        'value',
        'currency',
        'is_doc',
        
        // Special fields
        'remote_charges',
        'is_insured',
        'eori_number',
        'vat_number',
        'ioss_number',
        'label_file',
        'routing_code',
    ];

    protected $casts = [
        'date_created' => 'datetime',
        'date_label_created' => 'datetime',
        'weight' => 'decimal:3',
        'vol_weight' => 'decimal:3',
        'charge_weight' => 'decimal:3',
        'value' => 'decimal:2',
        'is_doc' => 'boolean',
        'is_insured' => 'boolean',
        'remote_charges' => 'boolean',
    ];

    // Status constants (from legacy)
    const STATUS_NEW = 10;
    const STATUS_INVALID = 11;
    const STATUS_READY_TO_PRINT = 12;
    const STATUS_LABEL_CREATED = 13;
    const STATUS_RECEIVED = 14;
    const STATUS_DISPATCHED = 16;
    const STATUS_INTRANSIT = 18;
    const STATUS_DELIVERED = 19;
    const STATUS_RECYCLED = 22;
    const STATUS_CANCELLED = 23;
    const STATUS_HOLD = 24;
    const STATUS_PROBLEM = 25;
    const STATUS_RETURNED = 27;

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function senderCountry(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'sender_country_id');
    }

    public function parcels(): HasMany
    {
        return $this->hasMany(Parcel::class);
    }

    public function charges(): HasMany
    {
        return $this->hasMany(ConsignmentCharge::class);
    }

    public function trackingData(): HasMany
    {
        return $this->hasMany(TrackingData::class);
    }

    // Scopes
    public function scopeReadyToPrint($query)
    {
        return $query->where('shipment_status', self::STATUS_READY_TO_PRINT);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('shipment_status', $status);
    }

    public function scopeByDateRange($query, $from, $to)
    {
        return $query->whereBetween('date_created', [$from, $to]);
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    // Business logic methods (from legacy Consignment::isValid())
    public function isValid(): array
    {
        $errors = [];

        // Receiver validation
        if (empty($this->address_line_1)) {
            $errors[] = 'Receiver address line 1 is required';
        }
        if (empty($this->city)) {
            $errors[] = 'Receiver city is required';
        }
        if (empty($this->postcode) && $this->country && $this->country->postcode_required) {
            $errors[] = 'Receiver postcode is required';
        }
        if (empty($this->contact)) {
            $errors[] = 'Receiver contact is required';
        }
        if (strlen($this->contact) > 35) {
            $errors[] = 'Receiver contact must be 35 characters or less';
        }
        if (strlen($this->company) > 35) {
            $errors[] = 'Receiver company must be 35 characters or less';
        }

        // Weight validation
        if ($this->weight <= 0) {
            $errors[] = 'Weight must be greater than 0';
        }

        // Service validation
        if (empty($this->service_id)) {
            $errors[] = 'Service is required';
        }

        // Description validation
        if (empty($this->description)) {
            $errors[] = 'Description is required';
        }

        // Number of pieces validation
        if ($this->number_pieces >= 100) {
            $errors[] = 'Number of pieces must be less than 100';
        }

        return $errors;
    }

    // Calculate volumetric weight
    public function calculateVolumetricWeight(): float
    {
        if (!$this->service) {
            return 0;
        }

        $denominator = $this->service->volumetric_denominator ?? 5000;
        
        $totalVolWeight = 0;
        foreach ($this->parcels as $parcel) {
            $volWeight = ($parcel->length * $parcel->width * $parcel->height) / $denominator;
            $totalVolWeight += $volWeight;
        }

        return round($totalVolWeight, 3);
    }

    // Get chargeable weight
    public function getChargeableWeight(): float
    {
        $actualWeight = $this->weight;
        $volWeight = $this->vol_weight ?? $this->calculateVolumetricWeight();
        
        return max($actualWeight, $volWeight);
    }
}
