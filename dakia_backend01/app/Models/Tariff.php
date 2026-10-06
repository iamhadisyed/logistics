<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tariff extends Model
{
    protected $table = 'tariffs';

    // tariffs.id is a plain integer, not an auto-increment column in the
    // restored legacy schema (unlike carriers/services) — rows are expected
    // to come from legacy data import with their original ids preserved.
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id',
        'user_account_id',
        'carrier_id',
        'service_id',
        'name',
        'status',
        'currency_id',
        'tariff_type',
        'start_date',
        'end_date',
        'description',
        'tariffs_pricing_rule_id',
    ];

    protected $casts = [
        'status' => 'boolean',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function details(): HasMany
    {
        return $this->hasMany(TariffDetail::class, 'tariffs_id');
    }
}
