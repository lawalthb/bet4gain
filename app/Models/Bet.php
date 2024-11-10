<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bet extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'game_id',
        'amount',
        'cashout_multiplier',
        'won_amount',
        'is_bot'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'cashout_multiplier' => 'decimal:2',
        'won_amount' => 'decimal:2',
        'is_bot' => 'boolean'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function game()
    {
        return $this->belongsTo(Game::class);
    }

    public function calculateWinnings()
    {
        return $this->amount * $this->cashout_multiplier;
    }
}
