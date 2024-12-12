<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
class TransactionController extends Controller
{
    public function pending()
    {
        $withdrawals = Transaction::where('type', 'withdrawal')
                                ->where('status', 'pending')
                                ->paginate(20);

        return view('admin.transactions.pending', compact('withdrawals'));
    }

    public function approve(Transaction $transaction)
    {
        $transaction->update(['status' => 'approved']);
        // Process withdrawal logic
        return back();
    }

    public function index()
    {
        $transactions = Transaction::with('user')
            ->latest()
            ->paginate(20);

        return view('admin.transactions.index', compact('transactions'));
    }

}

