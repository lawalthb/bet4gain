<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'phone_number',
        'password',
        'image',
        'referral_code',
        'is_active',
        'is_ban',
        'user_role_id',
        'wallet_balance',
        'withdraw_pin'
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


    public function getWinningsAttribute()
    {
        return $this->bets()->sum('won_amount');
    }
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

}

