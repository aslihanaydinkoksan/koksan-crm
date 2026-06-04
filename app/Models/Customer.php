<?php

namespace App\Models;

use App\Traits\HasDynamicFields;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\MorphMany;
class Customer extends Model
{
    use SoftDeletes, HasDynamicFields; // Dinamik form motoru entegre edildi

    protected $fillable = [
        'company_name',
        'tax_number',
        'email',
        'phone',
        'status',
        'assigned_user_id',
        'custom_data',
    ];

    protected $casts = [
        'custom_data' => 'array', // JSON verisini otomatik PHP Array'e çevirir
    ];

    /**
     * Müşterinin atandığı (sorumlu) kullanıcı
     */
    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_user_id');
    }
    /**
     * Bu müşteriye gönderilen numuneler (Polimorfik İlişki)
     */
    public function samples(): MorphMany
    {
        return $this->morphMany(Sample::class, 'receivable');
    }
}
