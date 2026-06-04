<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VisitResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'visit_date' => $this->visit_date?->format('Y-m-d H:i'),
            'reason' => $this->reason,
            'contact_persons' => $this->contact_persons ?? [],
            'observations' => $this->observations,
            'result' => $this->result,

            // İlişkiler (Eager Load ile geldiyse)
            'customer' => $this->whenLoaded('customer', function () {
                return [
                    'id' => $this->customer->id,
                    'company_name' => $this->customer->company_name,
                ];
            }),
            'user' => $this->whenLoaded('user', function () {
                return [
                    'id' => $this->user->id,
                    'name' => $this->user->name,
                ];
            }),

            'custom_data' => $this->custom_data ?? [],
        ];
    }
}
