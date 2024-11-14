<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'reference',
        'amount',
        'gateway_response',
        'paid_at',
        'channel',
        'currency',
        'ip_address',
        'metadata',
        'fees',
        'authorization_url',
        'status',
        'others',
        'domain',
        'email',
        'phone',
        'callback_url',
        'type',
        'payment_method',
        'transaction_hash',
        'bank_name',
        'account_number',
        'account_name'
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    }

