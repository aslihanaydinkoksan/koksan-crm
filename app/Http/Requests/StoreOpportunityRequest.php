<?php

namespace App\Http\Requests;

use App\Models\Opportunity;
use Illuminate\Foundation\Http\FormRequest;

class StoreOpportunityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() ? $this->user()->can('opportunities.create') : true;
    }

    public function rules(): array
    {
        $staticRules = [
            'customer_id' => ['required', 'integer', 'exists:customers,id'],
            'title' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0'],
            'currency' => ['required', 'string', 'size:3'], // Tam olarak 3 karakter (USD, EUR vb.)
            'expected_decision_date' => ['nullable', 'date'],
            'stage' => ['required', 'string', 'in:duyum,gorusme,teklif,kazanildi,kaybedildi'],
            'competitor' => ['nullable', 'string', 'max:255'],
            'details' => ['nullable', 'string'],
        ];

        // EAV Motoru: Dinamik form kurallarını ekle
        $dynamicRules = Opportunity::getDynamicValidationRules();

        return array_merge($staticRules, $dynamicRules);
    }
}
