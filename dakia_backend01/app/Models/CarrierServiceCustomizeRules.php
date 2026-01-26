<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CarrierServiceCustomizeRules extends Model
{
    protected $table = 'carrier_service_customize_rules';
    
    public $incrementing = false;
    public $timestamps = false;
    
    protected $fillable = [
        'id',
        'user_account_id',
        'serviceid',
        'agent_id',
        'from_weight',
        'to_weight',
        'status',
    ];

    protected $casts = [
        'from_weight' => 'decimal:2',
        'to_weight' => 'decimal:2',
        'status' => 'integer',
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
     * Scope to get active rules
     */
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    /**
     * Find rule for specific user, service, and weight
     */
    public static function findRule($userId, $serviceId, $weight)
    {
        return self::where('user_account_id', $userId)
            ->where('serviceid', $serviceId)
            ->where('from_weight', '<=', $weight)
            ->where('to_weight', '>=', $weight)
            ->where('status', 1)
            ->first();
    }
}
