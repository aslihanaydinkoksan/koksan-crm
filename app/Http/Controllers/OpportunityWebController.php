<?php

namespace App\Http\Controllers;

class OpportunityWebController extends Controller
{
    public function index()
    {
        return view('opportunities.index');
    }
}