<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Role extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'hierarchy_weight',
        'is_system',
        'description',
    ];

    protected $casts = [
        'is_system' => 'boolean',
        'hierarchy_weight' => 'integer',
    ];

    /**
     * Role ait izinler (Permissions)
     */
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class);
    }

    /**
     * Role sahip kullanıcılar (Users)
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }
}