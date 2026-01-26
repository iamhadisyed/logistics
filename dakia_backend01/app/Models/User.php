<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'user_account_id',
        'user_type',
        'user_name',
        'active_flag',
        'is_deleted',
    ];

    /**
     * Scope a query to only include active and non-deleted users.
     */
    public function scopeActive($query)
    {
        return $query->where('active_flag', 1)->where('is_deleted', 0);
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    /**
     * Get the account this user belongs to
     */
    public function userAccount()
    {
        return $this->belongsTo(UserAccount::class, 'user_account_id');
    }

    /**
     * The groups that belong to the user.
     */
    public function groups()
    {
        return $this->belongsToMany(Group::class, 'user_departments', 'user_id', 'department_id');
    }

    /**
     * Get all permissions inherited from all user's active groups.
     */
    public function getInheritedPermissions()
    {
        return Permission::whereHas('groups', function($query) {
            $query->whereHas('users', function($q) {
                $q->where('users.id', $this->id);
            })->where('groups.is_active', 1)->where('groups.is_deleted', 0);
        })->where('is_active', 1)->where('is_deleted', 0)->get();
    }
}
