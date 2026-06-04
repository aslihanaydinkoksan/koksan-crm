<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\DynamicField;
use App\Models\Sample;

class SampleDynamicFieldSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DynamicField::updateOrCreate(
            [
                'model_type' => Sample::class,
                'name' => 'shipping_company',
            ],
            [
                'label' => 'Kargo Firması',
                'type' => 'select',
                'options' => ['Yurtiçi', 'MNG', 'DHL', 'Aras', 'UPS'],
                'is_required' => true,
                'order_column' => 1,
            ]
        );
    }
}
