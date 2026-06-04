<?php

namespace App\Models;

use App\Traits\HasDynamicFields;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sample extends Model
{
    use SoftDeletes, HasDynamicFields;

    protected $fillable = [
        'subject',
        'tracking_number',
        'sent_at',
        'status',
        'receivable_type',
        'receivable_id',
        'custom_data',
    ];

    protected $casts = [
        'sent_at' => 'date',
        'custom_data' => 'array',
    ];

    /**
     * Numunenin alıcısı (Müşteri veya Şahıs olabilir)
     */
    public function receivable(): MorphTo
    {
        return $this->morphTo();
    }
}