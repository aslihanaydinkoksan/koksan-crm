<?php

namespace App\Actions\Visit;

use App\Models\Visit;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class GetVisitsAction
{
    /**
     * N+1 sorununu önleyerek filtreli ve sayfalanmış ziyaret listesini döndürür.
     */
    public function execute(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return Visit::query()
            // Eager Loading ile RAM şişmesini önle (Sadece gereken kolonları çekiyoruz)
            ->with(['customer:id,company_name', 'user:id,name'])

            // Ziyaret Sebebine Göre Filtre
            ->when(isset($filters['reason']), function ($query) use ($filters) {
                $query->where('reason', $filters['reason']);
            })

            // Müşteriye Göre Filtre
            ->when(isset($filters['customer_id']), function ($query) use ($filters) {
                $query->where('customer_id', $filters['customer_id']);
            })

            // En güncel ziyaretler en üstte
            ->orderBy('visit_date', 'desc')
            ->paginate($perPage);
    }
}
