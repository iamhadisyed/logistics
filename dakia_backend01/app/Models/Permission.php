<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    protected $table = 'permissions';
    public $timestamps = false;
    public $incrementing = false;

    protected $fillable = [
        'id',
        'lang_key', 
        'parent_id', 
        'file_name', 
        'description', 
        'query_string',
        'icon',
        'sort_order',
        'is_menu_item',
        'is_active', 
        'is_deleted'
    ];

    public function children()
    {
        return $this->hasMany(Permission::class, 'parent_id', 'id')
                    ->where('is_deleted', 0)
                    ->orderBy('sort_order', 'asc');
    }

    public function parent()
    {
        return $this->belongsTo(Permission::class, 'parent_id', 'id');
    }

    /**
     * Scope a query to only include menu items.
     */
    public function scopeMenuItem($query)
    {
        return $query->where('is_menu_item', 1);
    }

    /**
     * Scope a query to only include active and non-deleted permissions.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', 1)->where('is_deleted', 0);
    }

    public function groups(): BelongsToMany
    {
        return $this->belongsToMany(Group::class, 'grouphaspermissions', 'perm_id', 'group_id');
    }
}
?>
