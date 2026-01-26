<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomizedServicesRouting extends Model
{
    protected $table = 'customizedservicesrouting';
    
    protected $fillable = [
        'customize_service_id',
        'service_id',
        'country_id',
        'from_weight',
        'to_weight',
        'status',
        'account_number',
    ];

    protected $casts = [
        'from_weight' => 'decimal:2',
        'to_weight' => 'decimal:2',
    ];

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class, 'service_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Service::class, 'customize_service_id');
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    // Check if routing exists for specific parameters
    public static function findRouting($productId, $countryId, $weight)
    {
        return self::where('customize_service_id', $productId)
            ->where('country_id', $countryId)
            ->where('from_weight', '<=', $weight)
            ->where('to_weight', '>=', $weight)
            ->where('status', 'active')
            ->first();
    }
}
