<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Service extends Model
{
    use HasFactory;

    // services.id IS a real auto-increment primary key (see the
    // create_services_table migration). This was incorrectly set to false,
    // which silently breaks every Service::create() call that doesn't
    // manually pass an id: Eloquent skips reading the real generated id
    // back, leaving the in-memory model's id null even though the row was
    // inserted correctly. 'id' stays in $fillable below so legacy-data
    // seeders can still assign explicit ids when needed.
    public $incrementing = true;
    public $timestamps = false;
    
    protected $fillable = [
        'id',
        'carrier_id',
        'name',
        'code',
        'carrier_service_code',
        'type', // D=Domestic, I=International, E=Europe Road, R=Return
        'active',
        'from_weight',
        'to_weight',
        'origin_country',
        'delivery_mode',
        'label_class_name', // CRITICAL: for dynamic label generation
        'wieght_type', // 1=Parcel, 2=Shipment
        'is_customized', // 0=Service, 1=Product
        'is_remotearea',
        'volumetric_denominator',
        'fuel_surcharge',
        'validation_type', // 'courier' or 'mail'
        'mail_type',
        'zone_type', // 'country' or 'postcode'
        'tariff_type', // 'single' or 'multi'
        'pre_sort',
        'is_untrack',
        'is_eori_required',
        'delivery_type', // 'all', 'business', 'residential'
        'max_length',
        'max_width',
        'max_height',
        'max_volumetric_weight',
        'insurance_available',
        // NOTE: 'max_weight' and 'tracking_flag' were here before but are
        // fabricated — confirmed against db_full_schema.json (the real
        // legacy schema dump): the real `services` table has no such
        // columns. Legacy code only ever calls a computed getMaxWeight()
        // (definition not found under logistic/main/ or logistic/Classes/
        // in a reasonable search — likely a magic accessor); tracking is
        // already covered by the real 'is_untrack' column below. Removed
        // rather than kept as dead/broken fields.
    ];

    protected $casts = [
        'active' => 'boolean',
        'is_customized' => 'boolean',
        'is_untrack' => 'boolean',
        'is_eori_required' => 'boolean',
        'insurance_available' => 'boolean',
        'from_weight' => 'decimal:2',
        'to_weight' => 'decimal:2',
        'fuel_surcharge' => 'decimal:2',
        'volumetric_denominator' => 'integer',
    ];

    // Service type constants
    const TYPE_DOMESTIC = 'D';
    const TYPE_INTERNATIONAL = 'I';
    const TYPE_EUROPE_ROAD = 'E';
    const TYPE_RETURN = 'R';

    // Relationships
    public function carrier(): BelongsTo
    {
        return $this->belongsTo(Carrier::class);
    }

    public function countries(): BelongsToMany
    {
        return $this->belongsToMany(Country::class, 'service_country_ttime', 'id_service', 'id_country')
            ->withPivot('transit_time');
    }

    public function agents(): BelongsToMany
    {
        return $this->belongsToMany(Agent::class, 'service_agent_mappings', 'serviceid', 'agentid')
            ->withPivot('from_weight', 'to_weight');
    }

    public function consignments(): HasMany
    {
        return $this->hasMany(Consignment::class);
    }

    public function customizedRouting(): HasMany
    {
        return $this->hasMany(CustomizedServicesRouting::class, 'service_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByCarrier($query, $carrierId)
    {
        return $query->where('carrier_id', $carrierId);
    }

    // Check if service supports a specific country
    public function supportsCountry($countryId): bool
    {
        return $this->countries()->where('id_country', $countryId)->exists();
    }

    // Get label generator class name
    public function getLabelGeneratorClass(): string
    {
        return 'App\\Services\\Labels\\' . ucfirst($this->label_class_name) . 'Label';
    }
}
