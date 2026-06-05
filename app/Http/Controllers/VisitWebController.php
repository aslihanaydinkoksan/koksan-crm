<?php

namespace App\Http\Controllers;

class VisitWebController extends Controller
{
    public function index()
    {
        return view('visits.index');
    }
}