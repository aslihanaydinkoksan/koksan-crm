<?php

namespace App\Http\Controllers;

use App\Actions\Visit\CreateVisitAction;
use App\Actions\Visit\GetVisitsAction;
use App\Http\Requests\StoreVisitRequest;
use App\Http\Resources\VisitResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class VisitController extends Controller
{
    /**
     * Saha ziyaretlerini listeler.
     */
    public function index(Request $request, GetVisitsAction $action): AnonymousResourceCollection
    {
        $filters = $request->only(['reason', 'customer_id']);
        $perPage = $request->integer('per_page', 15);

        $visits = $action->execute($filters, $perPage);

        return VisitResource::collection($visits)->additional([
            'success' => true,
            'message' => 'Ziyaretler başarıyla listelendi.',
        ]);
    }

    /**
     * Yeni bir ziyaret kaydı oluşturur.
     */
    public function store(StoreVisitRequest $request, CreateVisitAction $action): JsonResponse
    {
        $validatedData = $request->validated();

        $visit = $action->execute($validatedData);

        return response()->json([
            'success' => true,
            'message' => 'Saha ziyareti kaydı başarıyla oluşturuldu.',
            'data' => new VisitResource($visit),
        ], 201);
    }
}
