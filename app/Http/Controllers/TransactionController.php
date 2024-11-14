<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Services\PaystackService;

class TransactionController extends Controller
{

    protected $paystackService;

    public function __construct(PaystackService $paystackService)
    {
        $this->paystackService = $paystackService;
    }


    public function initiateDeposit(Request $request)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:100',
            'payment_method' => 'nullable|string'
        ]);

        $amount = $validated['amount'] * 100; // Convert to kobo/cents

        $reference = 'DEP' . Str::random(17);

        $paymentData = [
            'email' => auth()->user()->email,
            'amount' => $amount,
            'reference' => $reference,
            'callback_url' => route('payment.callback'),
            'metadata' => [
                'user_id' => auth()->id(),
                'type' => 'deposit'
            ]
        ];

        $response = $this->paystackService->initializePayment($paymentData);

        if ($response['status']) {
            Transaction::create([
                'user_id' => auth()->id(),
                'reference' => $reference,
                'amount' => $validated['amount'],
                'type' => 'Deposit',
                'payment_method' => 'Paystack',
                'email' => auth()->user()->email,
                'phone' => auth()->user()->phone_number,
                'ip_address' => $request->ip(),
                'status' => 'Pending',
                'authorization_url' => $response['data']['authorization_url']
            ]);

            return response()->json([
                'success' => true,
                'authorization_url' => $response['data']['authorization_url']
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Payment initialization failed'
        ], 400);
    }

    public function handleWebhook(Request $request)
    {
        $payload = $request->all();
        $signature = $request->header('x-paystack-signature');

        if (!$this->verifyWebhookSignature($signature, $payload)) {
            return response()->json(['status' => 'Invalid signature'], 400);
        }

        $event = $payload['event'];
        $data = $payload['data'];

        if ($event == 'charge.success') {
            $transaction = Transaction::where('reference', $data['reference'])->first();

            if ($transaction) {
                $transaction->update([
                    'status' => 'Success',
                    'gateway_response' => $data['gateway_response'],
                    'paid_at' => $data['paid_at']
                ]);

                $transaction->user->increment('wallet_balance', $transaction->amount);
            }
        }

        return response()->json(['status' => 'Webhook handled']);
    }

    private function verifyWebhookSignature($signature, $payload)
    {
        $computedSignature = hash_hmac('sha512', json_encode($payload), config('paystack.secretKey'));
        return hash_equals($signature, $computedSignature);
    }




    public function initiateWithdrawal(Request $request)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:1',
            'bank_name' => 'required|string',
            'account_number' => 'required|string',
            'account_name' => 'required|string'
        ]);

        if (auth()->user()->wallet_balance < $validated['amount']) {
            return response()->json([
                'success' => false,
                'message' => 'Insufficient balance'
            ], 400);
        }

        $transaction = Transaction::create([
            'user_id' => auth()->id(),
            'reference' => 'WIT' . Str::random(17),
            'amount' => $validated['amount'],
            'type' => 'Withdrawal',
            'bank_name' => $validated['bank_name'],
            'account_number' => $validated['account_number'],
            'account_name' => $validated['account_name'],
            'email' => auth()->user()->email,
            'phone' => auth()->user()->phone_number,
            'ip_address' => $request->ip(),
            'status' => 'Pending'
        ]);

        return response()->json([
            'success' => true,
            'transaction' => $transaction
        ]);
    }

    public function giveBonus(User $user, $amount)
    {
        $transaction = Transaction::create([
            'user_id' => $user->id,
            'reference' => 'BON' . Str::random(17),
            'amount' => $amount,
            'type' => 'Bonus',
            'email' => $user->email,
            'phone' => $user->phone_number,
            'ip_address' => request()->ip(),
            'status' => 'Success'
        ]);

        $user->increment('wallet_balance', $amount);

        return response()->json([
            'success' => true,
            'transaction' => $transaction
        ]);
    }
    public function getTransactions()
    {
        $transactions = Transaction::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'transactions' => $transactions,
            'balance' => auth()->user()->wallet_balance
        ]);
    }

    public function handleCallback(Request $request)
    {
        $reference = $request->reference;
        $transaction = Transaction::where('reference', $reference)->first();

        if ($transaction) {
            $paystack = new PaystackService();
            $response = $paystack->verifyPayment($reference);

            if ($response['status'] && $response['data']['status'] === 'success') {
                // Update transaction status
                $transaction->update([
                    'status' => 'Success',
                    'gateway_response' => $response['data']['gateway_response'],
                    'paid_at' => now()
                ]);

                // Credit user wallet
                $transaction->user->increment('wallet_balance', $transaction->amount);

                return redirect()->route('wallet')
                    ->with('success', 'Payment completed successfully');
            }
        }

        return redirect()->route('wallet')
            ->with('error', 'Payment verification failed');
    }

    public function getUserBalance()
    {
        $balance = auth()->user()->wallet_balance;

        return response()->json([
            'balance' => $balance,
            'success' => true
        ]);
    }
}
