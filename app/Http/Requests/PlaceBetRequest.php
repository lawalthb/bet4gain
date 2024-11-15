<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PlaceBetRequest extends FormRequest
{
    public function rules()
    {
        return [
            'amount' => 'required|numeric|min:1',
            'auto_cashout' => 'nullable|numeric|min:1.1',
            'game_id' => 'required|exists:games,id'
        ];
    }
}
