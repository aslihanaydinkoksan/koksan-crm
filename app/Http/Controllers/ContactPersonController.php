<?php

namespace App\Http\Controllers;

use App\Models\ContactPerson;
use App\Http\Requests\StoreContactPersonRequest;
use Illuminate\Http\Request;

class ContactPersonController extends Controller
{
    public function index(Request $request)
    {
        $contacts = ContactPerson::where('customer_id', $request->customer_id)->latest()->get();
        return response()->json(['success' => true, 'data' => $contacts]);
    }

    public function store(StoreContactPersonRequest $request)
    {
        $contact = ContactPerson::create($request->validated());
        return response()->json(['success' => true, 'message' => 'Kişi eklendi.', 'data' => $contact]);
    }
}
