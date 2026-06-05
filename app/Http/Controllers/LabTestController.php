<?php

namespace App\Http\Controllers;

use App\Actions\LabTest\CreateLabTestAction;
use App\Actions\LabTest\GetLabTestsAction;
use App\Http\Requests\StoreLabTestRequest;
use App\Http\Resources\LabTestResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LabTestController extends Controller
{
    public function index(Request $request, GetLabTestsAction $action)
    {
        $tests = $action->execute($request->only(['customer_id', 'status']), $request->integer('per_page', 15));
        return LabTestResource::collection($tests)->additional(['success' => true]);
    }

    public function store(StoreLabTestRequest $request, CreateLabTestAction $action): JsonResponse
    {
        $labTest = $action->execute($request->validated());
        return response()->json([
            'success' => true,
            'message' => 'Laboratuvar testi başarıyla kaydedildi.',
            'data' => new LabTestResource($labTest),
        ], 201);
    }
}
