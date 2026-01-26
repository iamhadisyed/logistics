<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserServicesRouting extends Model
{
    protected $table = 'user_services_routings';
    
    public $incrementing = false;
    public $timestamps = false;
    
    protected $fillable = [
        'id',
        'user_account_id',
        'service_id',
        'country_id',
        'from_weight',
        'to_weight',
        'status',
        'is_remotearea',
        'is_over_label',
        'added_by',
        'is_agreed',
        'label_charges',
        'is_dead_weight',
        'is_over_size',
        'agent_id',
    ];

    protected $casts = [
        'from_weight' => 'decimal:2',
        'to_weight' => 'decimal:2',
        'is_agreed' => 'boolean',
        'status' => 'integer',
    ];

    /**
     * Get the service for this routing
     */
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class, 'service_id');
    }

    /**
     * Get the country for this routing
     */
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'country_id');
    }

    /**
     * Get the agent for this routing
     */
    public function agent(): BelongsTo
    {
        return $this->belongsTo(Agent::class, 'agent_id');
    }

    /**
     * Scope to get active routings
     */
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    /**
     * Scope to get agreed routings
     */
    public function scopeAgreed($query)
    {
        return $query->where('is_agreed', true);
    }
}
