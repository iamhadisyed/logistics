<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'name',
        'iso',
        'region',
        'postcode_required',
    ];

    protected $casts = [
        'postcode_required' => 'boolean',
    ];

    // Relationships
    public function services()
    {
        return $this->belongsToMany(Service::class, 'service_country_ttime', 'id_country', 'id_service')
            ->withPivot('transit_time');
    }

    public function consignments()
    {
        return $this->hasMany(Consignment::class);
    }

    // Scopes
    public function scopeByRegion($query, $region)
    {
        return $query->where('region', $region);
    }
}
