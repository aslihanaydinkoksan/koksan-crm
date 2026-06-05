<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LabTestResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'test_type' => $this->test_type,
            'status' => $this->status,
            'test_date' => $this->test_date?->format('Y-m-d H:i'),
            'result_summary' => $this->result_summary,
            'customer' => $this->whenLoaded('customer', fn() => [
                'id' => $this->customer->id,
                'company_name' => $this->customer->company_name,
            ]),
            'machine' => $this->whenLoaded('machine', fn() => [
                'id' => $this->machine->id,
                'name' => $this->machine->brand . ' ' . $this->machine->model_name,
            ]),
            'custom_data' => $this->custom_data ?? [],
        ];
    }
}
