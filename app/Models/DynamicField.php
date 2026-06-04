<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class DynamicField extends Model
{
    protected $fillable = [
        'model_type',
        'name',
        'label',
        'type',
        'is_required',
        'options',
        'order_column',
    ];

    protected $casts = [
        'is_required' => 'boolean',
        'options' => 'array', // JSON verisini otomatik Array'e dönüştürür
        'order_column' => 'integer',
    ];

    /**
     * Alanları arayüz için sıraya sokan Local Scope
     */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('order_column', 'asc');
    }
}