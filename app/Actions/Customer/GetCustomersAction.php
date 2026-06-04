<?php

namespace App\Actions\Customer;

use App\Models\Customer;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class GetCustomersAction
{
    /**
     * Filtreleri uygulayıp, sayfalanmış müşteri listesini döndürür.
     */
    public function execute(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return Customer::query()
            // N+1 Problemini çözmek için Eager Load. Sadece id ve name kolonlarını çekerek belleği koruyoruz.
            ->with('assignedUser:id,name')
            
            // Şartlı filtreleme: Eğer 'status' filtresi gönderilmişse uygula
            ->when(isset($filters['status']), function ($query) use ($filters) {
                $query->where('status', $filters['status']);
            })
            
            // En son eklenenler en üstte görünsün
            ->latest()
            
            // API standartları için sayfalandırma
            ->paginate($perPage);
    }
}