<?php

namespace App\Actions\LabTest;

use App\Models\LabTest;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class GetLabTestsAction
{
    public function execute(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return LabTest::query()
            ->with(['customer:id,company_name', 'machine:id,brand,model_name'])
            ->when(isset($filters['customer_id']), fn($q) => $q->where('customer_id', $filters['customer_id']))
            ->when(isset($filters['status']), fn($q) => $q->where('status', $filters['status']))
            ->orderBy('test_date', 'desc')
            ->paginate($perPage);
    }
}
