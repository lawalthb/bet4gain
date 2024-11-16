<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SpinBet extends Model
{
    protected $fillable = [
        'result',
        'multiplier',
        'status',
        'started_at',
        'ended_at'
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'ended_at' => 'datetime'
    ];

    public function bets()
    {
        return $this->hasMany(SpinBet::class);
    }
}
