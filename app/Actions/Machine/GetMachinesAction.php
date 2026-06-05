<?php

namespace App\Actions\Machine;

use App\Models\Machine;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class GetMachinesAction
{
    public function execute(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return Machine::query()
            ->with('customer:id,company_name')
            ->when(isset($filters['customer_id']), fn($q) => $q->where('customer_id', $filters['customer_id']))
            ->when(isset($filters['brand']), fn($q) => $q->where('brand', $filters['brand']))
            ->latest()
            ->paginate($perPage);
    }
}
