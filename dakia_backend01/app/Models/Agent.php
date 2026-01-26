<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Agent extends Model
{
    protected $table = 'agentdata';
    
    protected $fillable = [
        'agent_name',
        'agent_type',
        'country_id',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    // Relationships
    public function services(): BelongsToMany
    {
        return $this->belongsToMany(Service::class, 'service_agent_mapping', 'agentid', 'serviceid')
            ->withPivot('from_weight', 'to_weight');
    }

    // Check if agent supports a specific weight range for a service
    public function supportsWeightRange($serviceId, $fromWeight, $toWeight): bool
    {
        return $this->services()
            ->where('serviceid', $serviceId)
            ->where('from_weight', '<=', $fromWeight)
            ->where('to_weight', '>=', $toWeight)
            ->exists();
    }
}
