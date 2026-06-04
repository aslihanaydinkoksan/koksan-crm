<?php

namespace App\Traits;

use App\Models\DynamicField;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Validation\Rule;

trait HasDynamicFields
{
    /**
     * Bu model sınıfına (class) ait tanımlanmış dinamik alanları getirir.
     * Statik tanımlandı çünkü alanlar satıra (row) değil, modele aittir.
     */
    public static function getDynamicFields(): Collection
    {
        return DynamicField::where('model_type', static::class)
            ->ordered()
            ->get();
    }

    /**
     * Dinamik alan konfigürasyonlarına göre Laravel Validasyon kurallarını üretir.
     * FormRequest sınıflarındaki rules() metoduna array_merge ile eklenecek.
     */
    public static function getDynamicValidationRules(): array
    {
        $rules = [];
        $fields = static::getDynamicFields();

        // custom_data payload'unun kendisinin bir array olması gerektiğini garantiye alıyoruz
        $rules['custom_data'] = ['nullable', 'array'];

        foreach ($fields as $field) {
            $fieldRules = [];
            
            // 1. Zorunluluk Kontrolü
            $fieldRules[] = $field->is_required ? 'required' : 'nullable';

            // 2. Tip Validasyonu
            switch ($field->type) {
                case 'number':
                    $fieldRules[] = 'numeric';
                    break;
                case 'boolean':
                    $fieldRules[] = 'boolean';
                    break;
                case 'date':
                    $fieldRules[] = 'date';
                    break;
                case 'select':
                    // Seçenekler (options) dizisi doluysa, gelen değerin bu dizi içinde olmasını zorunlu kılıyoruz
                    if (!empty($field->options)) {
                        $fieldRules[] = Rule::in($field->options);
                    }
                    break;
                case 'text':
                default:
                    $fieldRules[] = 'string';
                    break;
            }

            // custom_data JSON kolonundaki ilgili anahtar (key) için kuralları atıyoruz
            // Örn: 'custom_data.packaging_type' => ['required', 'string', Rule::in(['kg', 'ton'])]
            $rules["custom_data.{$field->name}"] = $fieldRules;
        }

        return $rules;
    }
}