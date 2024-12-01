<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class BankAccountController extends Controller
{
    public function getBanks()
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . config('services.paystack.secret_key')
        ])->get('https://api.paystack.co/bank');

        return response()->json($response->json()['data']);
    }

    public function createTransferRecipient(Request $request)
    {
        $request->validate([
            'account_number' => 'required|string|size:10',
            'bank_code' => 'required|string'
        ]);

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . config('services.paystack.secret_key')
        ])->post('https://api.paystack.co/transferrecipient', [
            'type' => 'nuban',
            'name' => auth()->user()->name,
            'account_number' => $request->account_number,
            'bank_code' => $request->bank_code,
            'currency' => 'NGN'
        ]);

        if ($response->successful()) {
            auth()->user()->update([
                'recipient_code' => $response['data']['recipient_code']
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Bank account added successfully'
            ]);
        }

        return response()->json([
            'error' => 'Could not verify bank account'
        ], 422);
    }
}
