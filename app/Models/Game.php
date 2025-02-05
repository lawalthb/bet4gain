<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    use HasFactory;

    protected $fillable = [
        'crash_point',
        'started_at',
        'ended_at',
        'is_completed'
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
        'is_completed' => 'boolean',
        'crash_point' => 'decimal:2'
    ];

    public function bets()
    {
        return $this->hasMany(Bet::class);
    }

    public function players()
    {
        return $this->belongsToMany(User::class, 'bets');
    }

    public static function current()
    {
        return self::where('is_completed', false)
            ->latest()
            ->first();
    }

    public function getTotalBetsAttribute()
    {
        return $this->bets()->sum('amount');
    }

    public function updateTotalBets()
{
    $this->total_bets = $this->bets()->sum('amount');
    $this->save();
}

}
