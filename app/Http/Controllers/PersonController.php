<?php

namespace App\Http\Controllers;

use App\Models\Person;
use Illuminate\Http\JsonResponse;

class PersonController extends Controller
{
    public function index(): JsonResponse
    {
        $people = Person::select('id', 'first_name', 'last_name')->latest()->get();

        return response()->json([
            'success' => true,
            'data' => $people
        ]);
    }
}
