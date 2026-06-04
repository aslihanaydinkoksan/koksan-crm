<?php

namespace App\Actions\Customer;

use App\Models\Customer;
use Illuminate\Support\Facades\DB;
use Throwable;

class UpdateCustomerAction
{
    /**
     * Müşteri kaydını doğrulanmış verilerle günceller.
     * * @param Customer $customer
     * @param array $data
     * @return Customer
     * @throws Throwable
     */
    public function execute(Customer $customer, array $data): Customer
    {
        return DB::transaction(function () use ($customer, $data) {
            
            // Validasyon katmanından sadece izin verilen alanlar (statik + dinamik) gelir.
            // Model tarafındaki $fillable ayarları da ek güvenlik sağlar.
            $customer->update($data);

            // İlişkilerle (örn: assignedUser) ilgili ek işlemler gerekirse burada yapılabilir.

            return $customer->fresh(); // Güncel veriyi veritabanından tazele
        });
    }
}