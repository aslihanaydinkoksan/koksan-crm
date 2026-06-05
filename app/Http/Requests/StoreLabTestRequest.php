<?php

namespace App\Http\Requests;

use App\Models\LabTest;
use Illuminate\Foundation\Http\FormRequest;

class StoreLabTestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() ? $this->user()->can('lab_tests.create') : true;
    }

    public function rules(): array
    {
        $staticRules = [
            'customer_id' => ['required', 'integer', 'exists:customers,id'],
            'machine_id' => ['nullable', 'integer', 'exists:machines,id'],
            'sample_id' => ['nullable', 'integer', 'exists:samples,id'],
            'test_type' => ['required', 'string', 'max:150'],
            'status' => ['required', 'string', 'in:bekliyor,test_ediliyor,onaylandi,red'],
            'test_date' => ['required', 'date'],
            'result_summary' => ['nullable', 'string'],
        ];

        return array_merge($staticRules, LabTest::getDynamicValidationRules());
    }
}
