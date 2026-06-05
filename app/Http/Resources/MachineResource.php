<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MachineResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'ownership' => $this->ownership,
            'brand' => $this->brand,
            'model_name' => $this->model_name,
            'serial_number' => $this->serial_number,
            'installed_at' => $this->installed_at?->format('Y-m-d'),
            'customer' => $this->whenLoaded('customer', fn() => [
                'id' => $this->customer->id,
                'company_name' => $this->customer->company_name,
            ]),
            'custom_data' => $this->custom_data ?? [],
        ];
    }
}
