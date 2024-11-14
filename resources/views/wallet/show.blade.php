@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto p-4 space-y-6">
    <!-- Success Alert -->
    @if(session('success'))
    <div class="bg-green-500 text-white p-4 rounded-lg shadow-md">
        {{ session('success') }}
    </div>
    @endif

    <!-- Error Alert -->
    @if(session('error'))
    <div class="bg-red-500 text-white p-4 rounded-lg shadow-md">
        {{ session('error') }}
    </div>
    @endif

    <!-- Wallet Balance Card -->
    <div class="bg-gray-800 p-6 rounded-lg shadow-lg">
        <h2 class="text-xl md:text-2xl font-bold mb-4">Wallet Balance: ₦{{ $wallet }}</h2>

        <!-- Transaction Actions -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Deposit Form -->
            <div class="bg-gray-700 p-4 rounded-lg">
                <h3 class="text-lg md:text-xl mb-4">Deposit</h3>
                <form method="POST" action="{{ route('deposit') }}" class="space-y-4">
                    @csrf
                    <div>
                        <input type="number"
                            name="amount"
                            class="w-full p-2 rounded bg-gray-600"
                            placeholder="Amount"
                            required>
                    </div>
                    <button type="submit"
                        class="w-full bg-green-600 p-2 rounded hover:bg-green-700 transition">
                        Deposit
                    </button>
                </form>
            </div>

            <!-- Withdraw Form -->
            <div class="bg-gray-700 p-4 rounded-lg">
                <h3 class="text-lg md:text-xl mb-4">Withdraw</h3>
                <form method="POST" action="{{ route('withdraw') }}" class="space-y-4">
                    @csrf
                    <div>
                        <input type="number"
                            name="amount"
                            class="w-full p-2 rounded bg-gray-600"
                            placeholder="Amount"
                            required>
                    </div>
                    <button type="submit"
                        class="w-full bg-red-600 p-2 rounded hover:bg-red-700 transition">
                        Withdraw
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Transaction History -->
    <div class="bg-gray-800 p-6 rounded-lg shadow-lg overflow-x-auto">
        <h3 class="text-xl md:text-2xl font-bold mb-4">Transaction History</h3>
        <div class="min-w-full">
            @foreach($transactions as $transaction)
            <div class="border-b border-gray-700 p-4 flex flex-col md:flex-row justify-between items-start md:items-center space-y-2 md:space-y-0">
                <span class="text-sm md:text-base">{{ ucfirst($transaction->type) }}</span>
                <span class="{{ $transaction->amount > 0 ? 'text-green-500' : 'text-red-500' }} text-sm md:text-base">
                    ₦{{ abs($transaction->amount) }}
                </span>
                <span class="text-gray-400 text-xs md:text-sm">
                    {{ $transaction->created_at->diffForHumans() }}
                </span>
            </div>
            @endforeach
        </div>

        <div class="mt-4">
            {{ $transactions->links() }}
        </div>
    </div>
</div>
@endsection
