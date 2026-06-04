<?php

namespace App\Actions\Customer;

use App\Models\Customer;

class DeleteCustomerAction
{
    /**
     * Müşteri kaydını güvenli bir şekilde siler (Soft Delete).
     */
    public function execute(Customer $customer): bool
    {
        return $customer->delete();
    }
}