<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Carrier extends Model
{
    use HasFactory;
    public $timestamps = false; // Legacy table 'carriers' has no created_at/updated_at columns based on audit
    
    protected $fillable = [
        'carrier',
        'carrier_display_name',
        'logo',
        'status', // 0=Inactive, 1=Active, 2=Deleted
        'carrier_id', // parent carrier
        'country_id',
        'currency_code',
        'cut_off_time',
        'zone_base', // 0=Country-based, 1=Zone-based
        'zone_type', // 'country' or 'postcode'
        'remotearea_check', // 'c'=Carrier level, 's'=Service level
        'is_gazetteer',
        'is_reconcile',
        'on_contract',
        'is_pallet',
    ];

    protected $casts = [
        'status' => 'integer',
        'zone_base' => 'boolean',
        'is_gazetteer' => 'boolean',
        'is_reconcile' => 'boolean',
        'on_contract' => 'boolean',
        'is_pallet' => 'boolean',
    ];

    // Status constants
    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;
    const STATUS_DELETED = 2;

    // Relationships
    public function services(): HasMany
    {
        return $this->hasMany(Service::class);
    }

    public function parentCarrier(): BelongsTo
    {
        return $this->belongsTo(Carrier::class, 'carrier_id');
    }

    public function subCarriers(): HasMany
    {
        return $this->hasMany(Carrier::class, 'carrier_id');
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    public function scopeNotDeleted($query)
    {
        return $query->where('status', '!=', self::STATUS_DELETED);
    }

    // Business logic: Deactivate carrier and all its services
    public function deactivate(): void
    {
        $this->status = self::STATUS_INACTIVE;
        $this->save();

        // Cascade to all services (from legacy logic)
        $this->services()->update(['active' => false]);
    }

    // Activate carrier
    public function activate(): void
    {
        $this->status = self::STATUS_ACTIVE;
        $this->save();
    }
}
