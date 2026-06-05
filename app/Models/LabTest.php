<?php

namespace App\Models;

use App\Traits\HasDynamicFields;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class LabTest extends Model
{
    use SoftDeletes, HasDynamicFields;

    protected $fillable = [
        'customer_id',
        'machine_id',
        'sample_id',
        'test_type',
        'status',
        'test_date',
        'result_summary',
        'custom_data',
    ];

    protected $casts = [
        'test_date' => 'datetime',
        'custom_data' => 'array',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function machine(): BelongsTo
    {
        return $this->belongsTo(Machine::class);
    }

    public function sample(): BelongsTo
    {
        return $this->belongsTo(Sample::class);
    }
}
