<?php

namespace App\Traits;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Cache;


/**
 * @mixin \Illuminate\Database\Eloquent\Model
 * @property int|string $id
 * @method \Illuminate\Database\Eloquent\Relations\BelongsToMany belongsToMany($related, $table = null, $foreignPivotKey = null, $relatedPivotKey = null, $parentKey = null, $relatedKey = null, $relation = null)
 */
trait HasRolesAndPermissions
{
    /**
     * Kullanıcıya ait roller
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class);
    }

    /**
     * Önbellekten kullanıcının tüm rol slug'larını getirir.
     */
    public function getCachedRoles(): array
    {
        $cacheKey = "user_{$this->id}_roles";

        // TTI / TTL süresini projenin büyüklüğüne göre ayarlayabiliriz (Örn: 24 saat)
        return Cache::remember($cacheKey, now()->addDays(1), function () {
            return $this->roles()->pluck('slug')->toArray();
        });
    }

    /**
     * Önbellekten kullanıcının sahip olduğu tüm izin slug'larını getirir (Rolleri üzerinden).
     */
    public function getCachedPermissions(): array
    {
        $cacheKey = "user_{$this->id}_permissions";

        return Cache::remember($cacheKey, now()->addDays(1), function () {
            // N+1 problemini engellemek için eager loading ile rollere bağlı izinleri çekiyoruz
            return $this->roles()
                ->with('permissions')
                ->get()
                ->pluck('permissions')
                ->flatten()
                ->pluck('slug')
                ->unique()
                ->toArray();
        });
    }

    /**
     * Kullanıcının belirtilen role sahip olup olmadığını kontrol eder.
     */
    public function hasRole(string $roleSlug): bool
    {
        return in_array($roleSlug, $this->getCachedRoles(), true);
    }

    /**
     * Kullanıcının belirtilen yetkiye sahip olup olmadığını kontrol eder.
     */
    public function hasPermissionTo(string $permissionSlug): bool
    {
        return in_array($permissionSlug, $this->getCachedPermissions(), true);
    }

    /**
     * Kullanıcının rol veya yetkisi değiştiğinde önbelleği temizleyen tetikleyici metot.
     * Yönetim panelinden (Service/Action katmanından) çağrılacak.
     */
    public function forgetCachedPermissions(): void
    {
        Cache::forget("user_{$this->id}_roles");
        Cache::forget("user_{$this->id}_permissions");
    }
}