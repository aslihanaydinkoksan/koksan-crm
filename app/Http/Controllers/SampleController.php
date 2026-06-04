<?php

namespace App\Http\Controllers;

use App\Actions\Sample\CreateSampleAction;
use App\Actions\Sample\GetSamplesAction;
use App\Http\Requests\StoreSampleRequest;
use App\Http\Resources\SampleResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class SampleController extends Controller
{
    /**
     * Numuneleri listeler.
     */
    public function index(Request $request, GetSamplesAction $action): AnonymousResourceCollection
    {
        $filters = $request->only(['status']);
        $perPage = $request->integer('per_page', 15);

        $samples = $action->execute($filters, $perPage);

        return SampleResource::collection($samples)->additional([
            'success' => true,
            'message' => 'Numuneler başarıyla listelendi.',
        ]);
    }

    /**
     * Yeni bir numune kaydı oluşturur.
     */
    public function store(StoreSampleRequest $request, CreateSampleAction $action): JsonResponse
    {
        // 1. Dinamik Form + Statik + Polimorfik Doğrulama
        $validatedData = $request->validated();

        // 2. İş mantığını çalıştır
        $sample = $action->execute($validatedData);

        // 3. Sonucu Resource formatında dön
        return response()->json([
            'success' => true,
            'message' => 'Numune başarıyla oluşturuldu.',
            'data' => new SampleResource($sample),
        ], 201);
    }
}
