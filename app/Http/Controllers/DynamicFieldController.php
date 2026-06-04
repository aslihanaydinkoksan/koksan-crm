<?php

namespace App\Http\Controllers;

use App\Models\DynamicField;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DynamicFieldController extends Controller
{
    /**
     * İlgili modele ait dinamik form şemasını JSON olarak döndürür.
     * Kullanım: /api/dynamic-fields?model_type=App\Models\Customer
     */
    public function index(Request $request): JsonResponse
    {
        // Gelen isteği doğrula
        $validated = $request->validate([
            'model_type' => ['required', 'string']
        ]);

        // Modele ait alanları sıralı şekilde çek
        $fields = DynamicField::where('model_type', $validated['model_type'])
            ->ordered()
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Dinamik form şeması getirildi.',
            'data' => $fields
        ]);
    }
}