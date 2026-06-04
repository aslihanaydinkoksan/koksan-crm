<?php

namespace App\Actions\Sample;

use App\Models\Sample;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class GetSamplesAction
{
    /**
     * Filtreleri uygulayıp, sayfalanmış numune listesini döndürür.
     */
    public function execute(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return Sample::query()
            // N+1 Problemini Önle: Polimorfik ilişkiyi tek sorguda yükle
            ->with('receivable')
            ->when(isset($filters['status']), function ($query) use ($filters) {
                $query->where('status', $filters['status']);
            })
            ->latest()
            ->paginate($perPage);
    }
}
