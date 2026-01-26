<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TrackingData extends Model
{
    protected $table = 'tracking_data';
    
    protected $fillable = [
        'consignment_id',
        'tracking_number',
        'status_code',
        'status_description',
        'location',
        'timestamp',
        'signature',
    ];

    protected $casts = [
        'timestamp' => 'datetime',
    ];

    public function consignment(): BelongsTo
    {
        return $this->belongsTo(Consignment::class);
    }
}
