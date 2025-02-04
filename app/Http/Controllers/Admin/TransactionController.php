<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;

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

    public function index(Request $request)
    {
        $query = Transaction::with('user');

        if ($request->type) {
            $query->where('type', $request->type);
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->date_range) {
            $dates = explode(' to ', $request->date_range);
            $query->whereBetween('created_at', $dates);
        }

        $totalAmount = $query->sum('amount');
        $transactions = $query->latest()->paginate(20);

        $chartData = [
            'daily' => Transaction::selectRaw('DATE(created_at) as date, SUM(amount) as total')
                ->groupBy('date')
                ->orderBy('date')
                ->pluck('total', 'date'),

            'status' => Transaction::selectRaw('status, COUNT(*) as count')
                ->groupBy('status')
                ->get()
        ];

        return view('admin.transactions.index', compact('transactions', 'chartData', 'totalAmount'));
    }
}
