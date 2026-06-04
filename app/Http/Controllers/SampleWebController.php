<?php

namespace App\Http\Controllers;

class SampleWebController extends Controller
{
    public function index()
    {
        return view('samples.index');
    }
}
