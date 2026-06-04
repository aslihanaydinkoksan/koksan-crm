<?php

namespace App\Http\Requests;

use App\Models\Customer;
use App\Models\Person;
use App\Models\Sample;
use Illuminate\Foundation\Http\FormRequest;

class StoreSampleRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Test aşamasındaysak true dön, aksi halde Gate mekanizmasını çalıştır
        return $this->user() ? $this->user()->can('samples.create') : true;
    }

    public function rules(): array
    {
        // 1. İstekten gelen polimorfik tipe göre hedef tabloyu belirle
        $type = $this->input('receivable_type');

        $targetTable = match ($type) {
            Customer::class => 'customers',
            Person::class => 'people',
            default => 'invalid_table', // Tip hatalıysa exists kuralını patlatmak için
        };

        // 2. Statik ve Polimorfik kurallar
        $staticRules = [
            'subject' => ['required', 'string', 'max:255'],
            'tracking_number' => ['nullable', 'string', 'max:100'],
            'sent_at' => ['nullable', 'date'],
            'status' => ['required', 'string', 'in:hazirlaniyor,kargoda,teslim_edildi'],

            // Tip kontrolü (Sadece Customer veya Person sınıfları kabul edilir)
            'receivable_type' => ['bail', 'required', 'string', 'in:' . Customer::class . ',' . Person::class],

            // Dinamik Tablo Kontrolü: Belirlenen tablodaki 'id' kolonunda bu ID var mı?
            'receivable_id' => ['required', 'integer', "exists:{$targetTable},id"],
        ];

        // 3. EAV / Dinamik form motorundan gelen JSON kurallarını al
        $dynamicRules = Sample::getDynamicValidationRules();

        // 4. Birleştir ve döndür
        return array_merge($staticRules, $dynamicRules);
    }
}
