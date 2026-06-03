<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\User;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Dinamik RBAC ve God Mode (Bypass) Akışı
        Gate::before(function (User $user, string $ability) {
            
            // 1. GOD MODE: Eğer kullanıcı Developer ise hiçbir şeye bakmadan onayı ver.
            if ($user->is_developer) {
                return true;
            }

            // 2. DİNAMİK CACHE KONTROLÜ: Kullanıcının önbellekteki yetki dizisinde bu 'ability' (slug) var mı?
            // Bu adım sayesinde hiçbir Gate::define() döngüsüne ihtiyacımız kalmaz.
            if ($user->hasPermissionTo($ability)) {
                return true;
            }

            // 3. FALLBACK: Eğer yetki yoksa 'null' döneriz ve Laravel izni otomatik olarak reddeder (403 fırlatır).
            // İleride class-based policy'ler eklemek istersen bu null dönüşü onların da çalışmasına izin verir.
            return null;
        });
    }
}
