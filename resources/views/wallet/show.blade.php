@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="bg-gray-800 p-6 rounded-lg">
        <h2 class="text-2xl font-bold mb-4">Wallet Balance: ${{ $wallet->balance }}</h2>

        <div class="grid grid-cols-2 gap-6">
            <div>
                <h3 class="text-xl mb-4">Deposit</h3>
                <form method="POST" action="{{ route('deposit') }}">
                    @csrf
                    <div class="mb-4">
                        <input type="number" name="amount" class="w-full p-2 rounded bg-gray-700" placeholder="Amount" required>
                    </div>
                    <button type="submit" class="w-full bg-green-600 p-2 rounded hover:bg-green-700">
                        Deposit
                    </button>
                </form>
            </div>

            <div>
                <h3 class="text-xl mb-4">Withdraw</h3>
                <form method="POST" action="{{ route('withdraw') }}">
                    @csrf
                    <div class="mb-4">
                        <input type="number" name="amount" class="w-full p-2 rounded bg-gray-700" placeholder="Amount" required>
                    </div>
                    <button type="submit" class="w-full bg-red-600 p-2 rounded hover:bg-red-700">
                        Withdraw
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="bg-gray-800 p-6 rounded-lg">
        <h3 class="text-xl font-bold mb-4">Transaction History</h3>
        <div class="space-y-2">
            @foreach($transactions as $transaction)
            <div class="flex justify-between items-center p-2 bg-gray-700 rounded">
                <span>{{ ucfirst($transaction->type) }}</span>
                <span class="{{ $transaction->amount > 0 ? 'text-green-500' : 'text-red-500' }}">
                    ${{ abs($transaction->amount) }}
                </span>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
