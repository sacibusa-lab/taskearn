<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FestiveReward extends Model
{
    protected $fillable = [
        'user_id',
        'festive_program_id',
        'amount',
        'status',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function festiveProgram()
    {
        return $this->belongsTo(FestiveProgram::class);
    }
}
