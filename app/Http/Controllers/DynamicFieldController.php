<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDynamicFieldRequest;
use App\Http\Requests\UpdateDynamicFieldRequest;
use App\Models\DynamicField;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DynamicFieldController extends Controller
{
    /**
     * Tüm dinamik alanları (veya modeleye göre filtrelenmiş) listeler.
     */
    public function index(Request $request): JsonResponse
    {
        $query = DynamicField::query()->ordered();

        if ($request->has('model_type')) {
            $query->where('model_type', $request->model_type);
        }

        return response()->json([
            'success' => true,
            'data' => $query->get()
        ]);
    }

    /**
     * Yeni bir dinamik form alanı ekler.
     */
    public function store(StoreDynamicFieldRequest $request): JsonResponse
    {
        $data = $request->validated();

        // Aynı modele aynı key'in eklenmesini engelle
        if (DynamicField::where('model_type', $data['model_type'])->where('name', $data['name'])->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Bu modüle bu veritabanı anahtarı (name) zaten eklenmiş.'
            ], 422);
        }

        // Eğer type 'select' ise virgülle ayrılmış veriyi array'e çevir
        if ($data['type'] === 'select' && !empty($data['options'])) {
            $data['options'] = array_map('trim', explode(',', $data['options']));
        } else {
            $data['options'] = null;
        }

        // Sıralama numarasını otomatik belirle (En sona ekle)
        $lastOrder = DynamicField::where('model_type', $data['model_type'])->max('order_column') ?? 0;
        $data['order_column'] = $lastOrder + 1;

        $field = DynamicField::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Dinamik alan başarıyla eklendi.',
            'data' => $field
        ], 201);
    }
    /**
     * Mevcut bir dinamik alanı günceller (name ve model_type hariç).
     */
    public function update(UpdateDynamicFieldRequest $request, DynamicField $dynamicField): JsonResponse
    {
        $data = $request->validated();

        if ($data['type'] === 'select' && !empty($data['options'])) {
            $data['options'] = array_map('trim', explode(',', $data['options']));
        } else {
            $data['options'] = null;
        }

        $dynamicField->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Dinamik alan başarıyla güncellendi.',
            'data' => $dynamicField
        ]);
    }

    /**
     * Dinamik alanı siler.
     */
    public function destroy(DynamicField $dynamicField): JsonResponse
    {
        $dynamicField->delete();

        return response()->json([
            'success' => true,
            'message' => 'Alan başarıyla silindi.'
        ]);
    }
}
