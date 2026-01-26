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

    public $incrementing = false;
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
        'max_weight',
        'max_volumetric_weight',
        'tracking_flag',
        'insurance_available',
    ];

    protected $casts = [
        'active' => 'boolean',
        'is_customized' => 'boolean',
        'is_untrack' => 'boolean',
        'is_eori_required' => 'boolean',
        'tracking_flag' => 'boolean',
        'insurance_available' => 'boolean',
        'from_weight' => 'decimal:2',
        'to_weight' => 'decimal:2',
        'max_weight' => 'decimal:2',
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
