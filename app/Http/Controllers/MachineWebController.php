<?php

namespace App\Http\Controllers;

class MachineWebController extends Controller
{
    public function index()
    {
        return view('machines.index');
    }
}
