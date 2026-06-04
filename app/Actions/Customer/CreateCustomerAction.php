<?php

namespace App\Actions\Customer;

use App\Models\Customer;
use Illuminate\Support\Facades\DB;
use Throwable;

class CreateCustomerAction
{
    /**
     * Doğrulanmış veri (validated data) ile yeni müşteri oluşturur.
     * * @param array $data
     * @return Customer
     * @throws Throwable
     */
    public function execute(array $data): Customer
    {
        // Olası ilişkisel kayıtlar (ileride eklenecek adres/yetkili vb.) için Transaction açıyoruz
        return DB::transaction(function () use ($data) {

            $customer = Customer::create([
                'company_name' => $data['company_name'],
                'tax_number' => $data['tax_number'] ?? null,
                'email' => $data['email'] ?? null,
                'phone' => $data['phone'] ?? null,
                'status' => $data['status'] ?? 'aday',
                'assigned_user_id' => $data['assigned_user_id'] ?? null,

                // FormRequest'ten doğrulandıktan sonra null değilse array olarak gelen dinamik veriler
                'custom_data' => $data['custom_data'] ?? [],
            ]);

            // İleride buraya bildirimler (In-App Notification) veya Event fırlatmaları eklenebilir.

            return $customer;
        });
    }
}
