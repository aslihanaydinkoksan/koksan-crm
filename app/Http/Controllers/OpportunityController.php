<?php

namespace App\Http\Controllers;

use App\Actions\Opportunity\CreateOpportunityAction;
use App\Actions\Opportunity\GetOpportunitiesAction;
use App\Http\Requests\StoreOpportunityRequest;
use App\Http\Resources\OpportunityResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class OpportunityController extends Controller
{
    /**
     * Fırsatları/Duyumları listeler.
     */
    public function index(Request $request, GetOpportunitiesAction $action): AnonymousResourceCollection
    {
        $filters = $request->only(['stage', 'customer_id']);
        $perPage = $request->integer('per_page', 15);

        $opportunities = $action->execute($filters, $perPage);

        return OpportunityResource::collection($opportunities)->additional([
            'success' => true,
            'message' => 'Fırsatlar başarıyla listelendi.',
        ]);
    }

    /**
     * Yeni bir fırsat kaydı oluşturur.
     */
    public function store(StoreOpportunityRequest $request, CreateOpportunityAction $action): JsonResponse
    {
        $validatedData = $request->validated();

        $opportunity = $action->execute($validatedData);

        return response()->json([
            'success' => true,
            'message' => 'Fırsat kaydı başarıyla oluşturuldu.',
            'data' => new OpportunityResource($opportunity),
        ], 201);
    }
}
