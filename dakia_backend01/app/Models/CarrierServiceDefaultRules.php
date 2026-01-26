<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CarrierServiceDefaultRules extends Model
{
    protected $table = 'carrier_service_default_rules';
    
    public $timestamps = false;
    
    protected $fillable = [
        'serviceid',
        'agent_id',
        'from_weight',
        'to_weight',
        'country_id',
    ];

    protected $casts = [
        'from_weight' => 'decimal:2',
        'to_weight' => 'decimal:2',
    ];

    /**
     * Get the service for this rule
     */
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class, 'serviceid');
    }

    /**
     * Get the agent for this rule
     */
    public function agent(): BelongsTo
    {
        return $this->belongsTo(Agent::class, 'agent_id');
    }

    /**
     * Get the country for this rule
     */
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'country_id');
    }

    /**
     * Find default rule for specific service and weight
     */
    public static function findDefaultRule($serviceId, $weight, $countryId = null)
    {
        $query = self::where('serviceid', $serviceId)
            ->where('from_weight', '<=', $weight)
            ->where('to_weight', '>=', $weight);
        
        if ($countryId) {
            $query->where('country_id', $countryId);
        }
        
        return $query->first();
    }
}
