<?php

namespace App\Models;

use App\Traits\HasDynamicFields;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Machine extends Model
{
    use SoftDeletes, HasDynamicFields;

    protected $fillable = [
        'customer_id',
        'ownership',
        'brand',
        'model_name',
        'serial_number',
        'installed_at',
        'custom_data',
    ];

    protected $casts = [
        'installed_at' => 'date',
        'custom_data' => 'array',
    ];

    /**
     * Makinenin ait olduğu / kurulu olduğu müşteri
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
}
