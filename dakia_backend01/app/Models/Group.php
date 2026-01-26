<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Group extends Model
{
    protected $table = 'groups';
    protected $primaryKey = 'group_id';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'group_id',
        'group_name',
        'group_slug',
        'group_desc',
        'group_type',
        'is_active',
        'added_by',
        'added_date',
        'is_deleted',
    ];

    /**
     * Scope a query to only include active and non-deleted groups.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', 1)->where('is_deleted', 0);
    }

    /**
     * The users that belong to the group.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_departments', 'department_id', 'user_id');
    }

    /**
     * The permissions that belong to the group.
     */
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'grouphaspermissions', 'group_id', 'perm_id');
    }
}
