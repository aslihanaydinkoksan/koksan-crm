<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomerResource extends JsonResource
{
    /**
     * Kaynağı bir diziye (array) dönüştürür.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'company_name' => $this->company_name,
            'tax_number' => $this->tax_number,
            'email' => $this->email,
            'phone' => $this->phone,
            'status' => $this->status,
            
            // JSON kolonu zaten modelde array olarak cast edilmişti, doğrudan iletiyoruz
            'custom_data' => $this->custom_data ?? [],
            
            // İlişki yüklendiğinde null hatası almamak ve sadece gerekli veriyi (id, name) dönmek için
            'assigned_user' => $this->whenLoaded('assignedUser', function () {
                return [
                    'id' => $this->assignedUser->id,
                    'name' => $this->assignedUser->name,
                ];
            }),

            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
        ];
    }
}