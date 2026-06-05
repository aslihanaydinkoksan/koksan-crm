<?php

namespace App\Actions\LabTest;

use App\Models\LabTest;
use Illuminate\Support\Facades\DB;

class CreateLabTestAction
{
    public function execute(array $data): LabTest
    {
        return DB::transaction(function () use ($data) {
            return LabTest::create([
                'customer_id' => $data['customer_id'],
                'machine_id' => $data['machine_id'] ?? null,
                'sample_id' => $data['sample_id'] ?? null,
                'test_type' => $data['test_type'],
                'status' => $data['status'] ?? 'bekliyor',
                'test_date' => $data['test_date'],
                'result_summary' => $data['result_summary'] ?? null,
                'custom_data' => $data['custom_data'] ?? [],
            ]);
        });
    }
}
