<?php

namespace App\Actions\Visit;

use App\Models\Visit;
use Illuminate\Support\Facades\DB;
use Throwable;
use Illuminate\Support\Facades\Auth;

class CreateVisitAction
{
    /**
     * Doğrulanmış verilerle yeni bir saha ziyareti kaydı oluşturur.
     * @throws Throwable
     */
    public function execute(array $data): Visit
    {
        return DB::transaction(function () use ($data) {
            return Visit::create([
                'customer_id' => $data['customer_id'],
                'user_id' => $data['user_id'] ?? Auth::id(),
                'visit_date' => $data['visit_date'],
                'reason' => $data['reason'],
                'contact_persons' => $data['contact_persons'] ?? [],
                'observations' => $data['observations'],
                'result' => $data['result'] ?? null,
                'custom_data' => $data['custom_data'] ?? [],
            ]);
        });
    }
}
