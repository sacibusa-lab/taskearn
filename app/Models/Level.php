<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Level extends Model
{
    protected $fillable = [
        'level',
        'deposit_amount',
        'weekly_payout',
        'description',
    ];

    protected $casts = [
        'deposit_amount' => 'decimal:2',
        'weekly_payout' => 'decimal:2',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }
}
