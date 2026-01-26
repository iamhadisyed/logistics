<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceAgentMapping extends Model
{
    protected $table = 'service_agent_mappings';
    public $timestamps = false;
    public $incrementing = false;

    protected $fillable = [
        'id',
        'serviceid',
        'agentid',
        'from_weight',
        'to_weight',
        // other fields omitted for brevity but id is key for manual management
    ];

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class, 'serviceid');
    }

    public function agent(): BelongsTo
    {
        return $this->belongsTo(Agent::class, 'agentid');
    }
}
