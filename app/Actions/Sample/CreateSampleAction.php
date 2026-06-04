<?php

namespace App\Actions\Sample;

use App\Models\Sample;
use Illuminate\Support\Facades\DB;
use Throwable;

class CreateSampleAction
{
    /**
     * Doğrulanmış veriyle yeni numune kaydı oluşturur.
     * @throws Throwable
     */
    public function execute(array $data): Sample
    {
        return DB::transaction(function () use ($data) {
            return Sample::create([
                'subject' => $data['subject'],
                'tracking_number' => $data['tracking_number'] ?? null,
                'sent_at' => $data['sent_at'] ?? null,
                'status' => $data['status'] ?? 'hazirlaniyor',
                'receivable_type' => $data['receivable_type'],
                'receivable_id' => $data['receivable_id'],
                'custom_data' => $data['custom_data'] ?? [],
            ]);
        });
    }
}
