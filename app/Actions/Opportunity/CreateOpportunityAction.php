<?php

namespace App\Actions\Opportunity;

use App\Models\Opportunity;
use Illuminate\Support\Facades\DB;
use Throwable;

class CreateOpportunityAction
{
    /**
     * Doğrulanmış verilerle yeni bir satış fırsatı oluşturur.
     * @throws Throwable
     */
    public function execute(array $data): Opportunity
    {
        return DB::transaction(function () use ($data) {
            return Opportunity::create([
                'customer_id' => $data['customer_id'],
                'title' => $data['title'],
                'amount' => $data['amount'],
                'currency' => strtoupper($data['currency']), // Veritabanına temiz girmesi için büyük harf yapıyoruz
                'expected_decision_date' => $data['expected_decision_date'] ?? null,
                'stage' => $data['stage'] ?? 'duyum',
                'competitor' => $data['competitor'] ?? null,
                'details' => $data['details'] ?? null,
                'custom_data' => $data['custom_data'] ?? [],
            ]);
        });
    }
}
