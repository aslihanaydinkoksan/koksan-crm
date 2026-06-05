<?php

namespace Database\Seeders;

use App\Models\DynamicField;
use App\Models\Visit;
use Illuminate\Database\Seeder;

class VisitDynamicFieldSeeder extends Seeder
{
    public function run(): void
    {
        DynamicField::updateOrCreate(
            [
                'model_type' => Visit::class,
                'name' => 'barcode_no',
            ],
            [
                'label' => 'Makine / Ürün Barkod No',
                'type' => 'text',
                'is_required' => false,
                'order_column' => 1,
            ]
        );
    }
}
