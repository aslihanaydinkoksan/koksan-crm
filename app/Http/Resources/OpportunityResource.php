<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OpportunityResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,

            // Finansal veriler
            'amount' => $this->amount,
            'currency' => $this->currency,
            'formatted_amount' => number_format($this->amount, 2, ',', '.') . ' ' . $this->currency, // Arayüz kolaylığı için hazır format

            'expected_decision_date' => $this->expected_decision_date?->format('Y-m-d'),
            'stage' => $this->stage,
            'competitor' => $this->competitor,
            'details' => $this->details,

            // Müşteri İlişkisi (Eager Load edildiyse döndür)
            'customer' => $this->whenLoaded('customer', function () {
                return [
                    'id' => $this->customer->id,
                    'company_name' => $this->customer->company_name,
                ];
            }),

            'custom_data' => $this->custom_data ?? [],
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
        ];
    }
}
