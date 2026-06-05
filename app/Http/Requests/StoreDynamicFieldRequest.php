<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDynamicFieldRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() ? $this->user()->can('settings.manage') : true;
    }

    public function rules(): array
    {
        return [
            'model_type' => ['required', 'string'],
            // name: Sadece küçük harf ve alt çizgi kabul eder (JSON key formatı)
            'name' => ['required', 'string', 'regex:/^[a-z_]+$/', 'max:50'],
            'label' => ['required', 'string', 'max:100'],
            'type' => ['required', 'string', 'in:text,number,date,select'],
            'options' => ['nullable', 'string', 'required_if:type,select'],
            'is_required' => ['required', 'boolean'],
        ];
    }

    // Arayüzden gelen 'on'/'off' boolean değerleri dönüştürme
    protected function prepareForValidation()
    {
        $this->merge([
            'is_required' => $this->boolean('is_required'),
        ]);
    }
}
