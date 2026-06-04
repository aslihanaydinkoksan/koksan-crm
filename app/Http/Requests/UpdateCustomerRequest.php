<?php

namespace App\Http\Requests;

use App\Models\Customer;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCustomerRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Gate::before mekanizmamız Developer için her türlü true dönecektir.
        return $this->user() ? $this->user()->can('customers.update') : true;
    }

    public function rules(): array
    {
        // Route parameter olarak gelen 'customer' modelinden ID'yi alıyoruz
        $customerId = $this->route('customer') ? $this->route('customer')->id : null;

        $staticRules = [
            'company_name' => [
                'required', 'string', 'max:255', 
                Rule::unique('customers', 'company_name')->ignore($customerId)
            ],
            'tax_number' => [
                'nullable', 'string', 'max:50', 
                Rule::unique('customers', 'tax_number')->ignore($customerId)
            ],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'status' => ['required', 'string', 'in:aday,aktif,pasif'],
            'assigned_user_id' => ['nullable', 'exists:users,id'],
        ];

        // HasDynamicFields trait'imizden dinamik validasyon kurallarını çek
        $dynamicRules = Customer::getDynamicValidationRules();

        // Statik ve dinamik kuralları birleştir
        return array_merge($staticRules, $dynamicRules);
    }
}