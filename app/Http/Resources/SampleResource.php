<?php

namespace App\Http\Resources;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SampleResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'subject' => $this->subject,
            'tracking_number' => $this->tracking_number,
            'sent_at' => $this->sent_at?->format('Y-m-d'),
            'status' => $this->status,
            'custom_data' => $this->custom_data ?? [],

            // Polimorfik Alıcı Bilgisini Çözümleme
            'receiver' => $this->whenLoaded('receivable', function () {
                // Alıcı Tipine göre ismi dinamik belirle
                $displayName = $this->receivable_type === Customer::class
                    ? $this->receivable->company_name
                    : trim($this->receivable->first_name . ' ' . $this->receivable->last_name);

                return [
                    'id' => $this->receivable->id,
                    'type' => $this->receivable_type, // Frontend yönlendirmeleri için gerekli
                    'display_name' => $displayName,
                ];
            }),

            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
        ];
    }
}
