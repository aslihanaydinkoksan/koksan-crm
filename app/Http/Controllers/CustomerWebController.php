<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;

class CustomerWebController extends Controller
{
    /**
     * Müşteri test arayüzünü döndürür.
     */
    public function index()
    {
        return view('customers.index');
    }
    /**
     * Müşteri 360° Detay (Customer 360 Profile) arayüzünü döndürür.
     */
    public function show(Customer $customer)
    {
        // Müşteri verisini view'a gönderiyoruz (Başlıkta ID, İsim ve Statü kullanmak için)
        return view('customers.show', compact('customer'));
    }
}
