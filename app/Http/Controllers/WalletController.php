<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    public function show()
    {
        $wallet = auth()->user()->wallet;
        $transactions = $wallet->transactions()->latest()->paginate(10);
        return view('wallet.show', compact('wallet', 'transactions'));
    }

    public function deposit(Request $request)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:1'
        ]);

        // Paystack integration would go here
        $wallet = auth()->user()->wallet;
        $wallet->updateBalance($validated['amount']);

        Transaction::create([
            'wallet_id' => $wallet->id,
            'amount' => $validated['amount'],
            'type' => 'deposit',
            'status' => 'completed'
        ]);

        return back()->with('success', 'Deposit successful!');
    }

    public function withdraw(Request $request)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:1'
        ]);

        $wallet = auth()->user()->wallet;

        if ($wallet->balance < $validated['amount']) {
            return back()->with('error', 'Insufficient balance');
        }

        $wallet->updateBalance(-$validated['amount']);

        Transaction::create([
            'wallet_id' => $wallet->id,
            'amount' => -$validated['amount'],
            'type' => 'withdrawal',
            'status' => 'pending'
        ]);

        return back()->with('success', 'Withdrawal request submitted!');
    }
}
