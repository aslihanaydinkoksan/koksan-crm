<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDynamicFieldRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() ? $this->user()->can('settings.manage') : true;
    }

    public function rules(): array
    {
        return [
            'label' => ['required', 'string', 'max:100'],
            'type' => ['required', 'string', 'in:text,number,date,select'],
            'options' => ['nullable', 'string', 'required_if:type,select'],
            'is_required' => ['required', 'boolean'],
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'is_required' => $this->boolean('is_required'),
        ]);
    }
}
