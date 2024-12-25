<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SpinGame extends Model
{
    protected $fillable = [
        'result_color',
        'multiplier',
        'is_completed',
        'started_at'
    ];

    protected $casts = [
        'is_completed' => 'boolean',
        'started_at' => 'datetime'
    ];

    public function bets()
    {
        return $this->hasMany(SpinBet::class);
    }
}
