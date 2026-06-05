<?php

namespace App\Http\Controllers;

class SettingsWebController extends Controller
{
    public function dynamicFields()
    {
        // İleride buraya yeni modül eklemek yeterli olacak
        $supportedModels = [
            'App\Models\Customer' => 'Müşteriler',
            'App\Models\Sample' => 'Numuneler',
            'App\Models\Opportunity' => 'Fırsatlar',
            'App\Models\Visit' => 'Ziyaretler',
            'App\Models\Machine' => 'Makineler',
            'App\Models\LabTest' => 'Laboratuvar Testleri',
        ];

        return view('settings.dynamic-fields', compact('supportedModels'));
    }
}
