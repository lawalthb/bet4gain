<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SpinBet extends Model
{
    protected $fillable = [
        'user_id',
        'spin_game_id',
        'amount',
        'color',
        'multiplier',
        'profit',
        'status'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function game()
    {
        return $this->belongsTo(SpinGame::class);
    }
}
