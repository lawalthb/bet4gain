<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bet extends Model
{
    protected $fillable = [
        'game_id',
        'user_id',
        'amount',
        'cashout_multiplier',
        'profit',
        'is_demo',
        'is_bot',
        'bot_name',
        'auto_cashout',
        'status'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'cashout_multiplier' => 'decimal:2',
        'profit' => 'decimal:2',
        'is_demo' => 'boolean',
        'is_bot' => 'boolean',
        'auto_cashout' => 'decimal:2'
    ];

    public function game()
    {
        return $this->belongsTo(Game::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeRealBets($query)
    {
        return $query->where('is_demo', false);
    }

    public function scopeBotBets($query)
    {
        return $query->where('is_bot', true);
    }

    public function scopeUserBets($query)
    {
        return $query->where('is_bot', false)->where('user_id', '!=', null);
    }
}
