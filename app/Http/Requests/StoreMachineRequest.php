<?php

namespace App\Http\Requests;

use App\Models\Machine;
use Illuminate\Foundation\Http\FormRequest;

class StoreMachineRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() ? $this->user()->can('machines.create') : true;
    }

    public function rules(): array
    {
        $staticRules = [
            'customer_id' => ['required', 'integer', 'exists:customers,id'],
            'ownership' => ['required', 'string', 'max:100'],
            'brand' => ['required', 'string', 'max:100'],
            'model_name' => ['required', 'string', 'max:255'],
            'serial_number' => ['nullable', 'string', 'max:150', 'unique:machines,serial_number'],
            'installed_at' => ['nullable', 'date'],
        ];

        return array_merge($staticRules, Machine::getDynamicValidationRules());
    }
}
