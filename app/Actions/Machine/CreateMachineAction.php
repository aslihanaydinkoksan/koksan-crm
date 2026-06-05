<?php

namespace App\Actions\Machine;

use App\Models\Machine;
use Illuminate\Support\Facades\DB;

class CreateMachineAction
{
    public function execute(array $data): Machine
    {
        return DB::transaction(function () use ($data) {
            return Machine::create([
                'customer_id' => $data['customer_id'],
                'ownership' => $data['ownership'],
                'brand' => $data['brand'],
                'model_name' => $data['model_name'],
                'serial_number' => $data['serial_number'] ?? null,
                'installed_at' => $data['installed_at'] ?? null,
                'custom_data' => $data['custom_data'] ?? [],
            ]);
        });
    }
}
