<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'balance',
        'is_bot'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'balance' => 'decimal:2',
        'is_bot' => 'boolean'
    ];

    public function bets()
    {
        return $this->hasMany(Bet::class);
    }

    public function games()
    {
        return $this->belongsToMany(Game::class, 'bets');
    }

    public function wallet()
    {
        return $this->hasOne(Wallet::class);
    }

    public function getWinningsAttribute()
    {
        return $this->bets()->sum('won_amount');
    }
}
