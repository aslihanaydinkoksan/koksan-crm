<?php

namespace App\Models;

use App\Traits\HasDynamicFields;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Visit extends Model
{
    use SoftDeletes, HasDynamicFields;

    protected $fillable = [
        'customer_id',
        'user_id',
        'visit_date',
        'reason',
        'contact_persons',
        'observations',
        'result',
        'custom_data',
    ];

    protected $casts = [
        'visit_date' => 'datetime',
        'contact_persons' => 'array', // JSON kolonunu otomatik diziye çevirir
        'custom_data' => 'array',
    ];

    /**
     * Ziyaret edilen müşteri
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Ziyareti gerçekleştiren personel (User)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
