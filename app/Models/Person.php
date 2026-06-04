<?php

namespace App\Models;

use App\Traits\HasDynamicFields;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Person extends Model
{
    use SoftDeletes, HasDynamicFields;

    protected $fillable = [
        'first_name',
        'last_name',
        'phone',
        'email',
        'custom_data',
    ];

    protected $casts = [
        'custom_data' => 'array',
    ];

    /**
     * Bu şahsa gönderilen numuneler (Polimorfik İlişki)
     */
    public function samples(): MorphMany
    {
        // Sample modeli üzerinde 'receivable' adıyla polimorfik bağlantı kuruyoruz
        return $this->morphMany(Sample::class, 'receivable');
    }
}