<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FestiveProgram extends Model
{
    protected $fillable = [
        'title',
        'description',
        'start_date',
        'end_date',
        'bonus_type',
        'bonus_value',
        'status',
        'banner',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'bonus_value' => 'decimal:2',
    ];

    public function rewards()
    {
        return $this->hasMany(FestiveReward::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active')
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now());
    }
}
