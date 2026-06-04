<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CustomerWebController extends Controller
{
    /**
     * Müşteri test arayüzünü döndürür.
     */
    public function index()
    {
        return view('customers.index');
    }
}