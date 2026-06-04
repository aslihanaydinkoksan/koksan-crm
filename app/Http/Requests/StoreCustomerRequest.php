<?php

namespace App\Http\Requests;

use App\Models\Customer;
use Illuminate\Foundation\Http\FormRequest;

class StoreCustomerRequest extends FormRequest
{
    /**
     * Kullanıcının bu işlemi yapmaya yetkisi var mı?
     */
    public function authorize(): bool
    {
        // Trait'ten ve AppServiceProvider'daki Gate::before'dan beslenen yetki kontrolümüz
        return $this->user() ? $this->user()->can('customers.create') : true;
    }

    /**
     * Validasyon kuralları (Statik + Dinamik birleşimi)
     */
    public function rules(): array
    {
        // 1. Sistemin zorunlu (statik) kolon kuralları
        $staticRules = [
            'company_name' => ['required', 'string', 'max:255', 'unique:customers,company_name'],
            'tax_number' => ['nullable', 'string', 'max:50', 'unique:customers,tax_number'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'status' => ['required', 'string', 'in:aday,aktif,pasif'],
            'assigned_user_id' => ['nullable', 'exists:users,id'],
        ];

        // 2. Dinamik EAV form kurallarını modelden çek (HasDynamicFields trait'i üzerinden)
        $dynamicRules = Customer::getDynamicValidationRules();

        // 3. İki kural setini birleştir ve döndür
        return array_merge($staticRules, $dynamicRules);
    }
}
