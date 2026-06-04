<?php

namespace App\Models;

use App\Traits\HasDynamicFields;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Opportunity extends Model
{
    use SoftDeletes, HasDynamicFields;

    protected $fillable = [
        'customer_id',
        'title',
        'amount',
        'currency',
        'expected_decision_date',
        'stage',
        'competitor',
        'details',
        'custom_data',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'expected_decision_date' => 'date',
        'custom_data' => 'array',
    ];

    /**
     * Fırsatın ait olduğu müşteri
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
}
