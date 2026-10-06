<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TariffDetail extends Model
{
    protected $table = 'tariffs_details';

    // Same as Tariff: plain integer id, expected from legacy data import.
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id',
        'tariffs_id',
        'from_zone_id',
        'to_zone_id',
        'weight_from',
        'weight_to',
        'weight_cost',
        'piece_cost',
        'formula',
    ];

    protected $casts = [
        'weight_from' => 'decimal:3',
        'weight_to' => 'decimal:3',
        'weight_cost' => 'decimal:2',
        'piece_cost' => 'decimal:2',
    ];

    public function tariff(): BelongsTo
    {
        return $this->belongsTo(Tariff::class, 'tariffs_id');
    }
}
