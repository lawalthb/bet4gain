<?php

namespace App\Http\Controllers;

use App\Models\Transaction;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Services\PaystackService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;

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
        $request->validate([
            'amount' => 'required|numeric|min:1000',
            'password' => 'required'
        ]);

        $user = auth()->user();

        if (!$user->recipient_code) {
            return response()->json(['error' => 'Bank account not set up'], 422);
        }

        if (!Hash::check($request->password, $user->password)) {
            return response()->json(['error' => 'Invalid password'], 422);
        }

        $amount = $request->amount;
        $fee = $amount * 0.20;
        $finalAmount = $amount - $fee;

        if ($user->wallet_balance < $amount) {
            return response()->json(['error' => 'Insufficient balance'], 422);
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . config('services.paystack.secret_key'),
                'Content-Type' => 'application/json',
            ])->post('https://api.paystack.co/transfer', [
                'source' => 'balance',
                'amount' => (int)($finalAmount * 100), // Convert to integer kobo
                'recipient' => $user->recipient_code,
                'reason' => 'Withdrawal from Bet4Gain',
                'currency' => 'NGN'
            ]);

            if ($response->successful() && $response->json()['status']) {
                $user->decrement('wallet_balance', $amount);

                Transaction::create([
                    'user_id' => $user->id,
                    'type' => 'Withdrawal',
                    'amount' => -$amount,
                    'fee' => $fee,
                    'status' => 'completed',
                    'reference' => $response['data']['reference']
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Withdrawal successful',
                    'new_balance' => $user->wallet_balance
                ]);
            }

            \Log::error('Paystack Transfer Error:', $response->json());
            return response()->json(['error' => 'Transfer failed: ' . $response->json()['message']], 500);
        } catch (\Exception $e) {
            \Log::error('Transfer Exception:', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Transfer service unavailable'], 500);
        }
    }

}
