<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\DynamicField;
use Illuminate\Database\Seeder;

class CustomerDynamicFieldSeeder extends Seeder
{
    public function run(): void
    {
        // App\Models\Customer modeli için "Ambalaj Tipi" dinamik alanını tanımlıyoruz.
        DynamicField::updateOrCreate(
            [
                'model_type' => Customer::class,
                'name' => 'packaging_type',
            ],
            [
                'label' => 'Ambalaj Tipi',
                'type' => 'select',
                // Options kolonu modelde 'array' cast edildiği için doğrudan dizi veriyoruz
                'options' => ['kg', 'ton', 'oktabin'],
                'is_required' => true,
                'order_column' => 1,
            ]
        );
    }
}
