<?php

namespace App\Http\Requests;

use App\Models\Visit;
use Illuminate\Foundation\Http\FormRequest;

class StoreVisitRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() ? $this->user()->can('visits.create') : true;
    }

    public function rules(): array
    {
        $staticRules = [
            'customer_id' => ['required', 'integer', 'exists:customers,id'],
            'user_id' => ['nullable', 'integer', 'exists:users,id'], // Null gelirse Action'da Auth::id() atayacağız
            'visit_date' => ['required', 'date'],
            'reason' => ['required', 'string', 'max:100'],
            'contact_persons' => ['nullable', 'array'], // Array formatında bekliyoruz
            'contact_persons.*' => ['string', 'max:255'],
            'observations' => ['required', 'string'],
            'result' => ['nullable', 'string'],
        ];

        // EAV Motoru dinamik kuralları
        $dynamicRules = Visit::getDynamicValidationRules();

        return array_merge($staticRules, $dynamicRules);
    }
}
