<?php

namespace App\Http\Controllers;

use App\Models\Transaction;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Services\PaystackService;
class WalletController extends Controller
{


    protected $paystackService;

    public function __construct(PaystackService $paystackService)
    {
        $this->paystackService = $paystackService;
    }

    public function show()
    {
        $wallet = auth()->user()->wallet_balance;

        $transactions = auth()->user()->transactions()->latest()->paginate(10);

        return view('wallet.show', compact('wallet', 'transactions'));
    }

    public function deposit(Request $request)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:100'
        ]);

        $reference = 'DEP' . Str::random(17);

        $paymentData = [
            'email' => auth()->user()->email,
            'amount' => $validated['amount'] * 100,
            'reference' => $reference,
            'callback_url' => route('transaction.callback'),
            'metadata' => [
                'user_id' => auth()->id(),
                'type' => 'deposit'
            ]
        ];

        $paystack = new PaystackService();
        $response = $paystack->initializePayment($paymentData);

        if ($response['status']) {
            Transaction::create([
                'user_id' => auth()->id(),
                'reference' => $reference,
                'amount' => $validated['amount'],
                'currency' => 'NGN',
                'type' => 'Deposit',
                'payment_method' => 'Paystack',
                'email' => auth()->user()->email,
                'phone' => auth()->user()->phone_number,
                'ip_address' => $request->ip(),
                'status' => 'Pending',
                'authorization_url' => $response['data']['authorization_url']
            ]);

            return redirect($response['data']['authorization_url']);
        }

        return redirect()->back()->with('error', 'Payment initialization failed');
    }

    public function withdraw(Request $request)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:1'
        ]);

        $wallet = auth()->user()->wallet_balance;

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
