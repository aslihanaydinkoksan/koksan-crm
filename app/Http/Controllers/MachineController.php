<?php

namespace App\Http\Controllers;

use App\Actions\Machine\CreateMachineAction;
use App\Actions\Machine\GetMachinesAction;
use App\Http\Requests\StoreMachineRequest;
use App\Http\Resources\MachineResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MachineController extends Controller
{
    public function index(Request $request, GetMachinesAction $action)
    {
        $filters = $request->only(['customer_id', 'brand']);
        $machines = $action->execute($filters, $request->integer('per_page', 15));

        return MachineResource::collection($machines)->additional(['success' => true]);
    }

    public function store(StoreMachineRequest $request, CreateMachineAction $action): JsonResponse
    {
        $machine = $action->execute($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Makine kaydı başarıyla oluşturuldu.',
            'data' => new MachineResource($machine),
        ], 201);
    }
}
