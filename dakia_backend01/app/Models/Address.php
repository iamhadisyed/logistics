<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Address extends Model
{
    public $timestamps = false;
    protected $fillable = [
        'phone_number',
        'company',
        'contact',
        'email',
        'address_line_1',
        'address_line_2',
        'address_line_3',
        'city',
        'country',
        'postcode',
        'user_id',
        'state',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
