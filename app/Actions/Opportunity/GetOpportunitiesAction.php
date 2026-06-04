<?php

namespace App\Actions\Opportunity;

use App\Models\Opportunity;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class GetOpportunitiesAction
{
    /**
     * Filtreleri uygulayıp, sayfalanmış fırsat listesini döndürür.
     */
    public function execute(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return Opportunity::query()
            // N+1 Sorununu Engelle: Sadece firmanın adını ve ID'sini çek (Performans)
            ->with('customer:id,company_name')

            // Fırsat Aşamasına Göre Filtreleme
            ->when(isset($filters['stage']), function ($query) use ($filters) {
                $query->where('stage', $filters['stage']);
            })

            // Müşteriye Göre Filtreleme
            ->when(isset($filters['customer_id']), function ($query) use ($filters) {
                $query->where('customer_id', $filters['customer_id']);
            })

            ->latest()
            ->paginate($perPage);
    }
}
