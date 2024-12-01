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
                    <img src="{{asset('assets/images/paystack.png')}}">
                </form>
            </div>

            <!-- Withdraw Form -->
            <div class="bg-gray-800 p-6 rounded-lg shadow-lg">
                <h3 class="text-xl font-bold mb-6 text-white">Withdraw Funds</h3>

                <form id="withdrawForm" onsubmit="processWithdrawal(event)" class="space-y-6">
                    @csrf
                    <div>
                        <label class="text-gray-300 mb-2 block">Amount (₦)</label>
                        <input type="number"
                            name="amount"
                            class="w-full p-3 rounded bg-gray-700 border border-gray-600 text-white"
                            placeholder="Enter amount"
                            min="1000"
                            required>
                        <div class="flex items-center mt-2 text-yellow-500">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"></path>
                            </svg>
                            <span class="text-sm">20% fee will be deducted</span>
                        </div>
                    </div>

                    <div>
                        <label class="text-gray-300 mb-2 block">Confirm Password</label>
                        <input type="password"
                            name="password"
                            class="w-full p-3 rounded bg-gray-700 border border-gray-600 text-white"
                            placeholder="Enter your password"
                            required>
                    </div>

                    <div class="bg-gray-700 p-4 rounded-lg space-y-2">
                        <h4 class="font-medium text-white mb-3">Important Information</h4>
                        <ul class="text-sm text-gray-300 space-y-2">
                            <li class="flex items-center">
                                <span class="mr-2">•</span>
                                Minimum withdrawal: ₦1,000
                            </li>
                            <li class="flex items-center">
                                <span class="mr-2">•</span>
                                20% service fee applies
                            </li>
                            <li class="flex items-center">
                                <span class="mr-2">•</span>
                                Instant transfer to your bank account
                            </li>
                        </ul>
                    </div>

                    <button type="submit"
                        class="w-full bg-green-600 p-3 rounded-lg font-medium text-white hover:bg-green-700 transition-colors duration-200 flex items-center justify-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        Withdraw Now
                    </button>
                </form>

                <div id="withdrawalStatus" class="mt-4 hidden">
                    <!-- Status messages will be inserted here -->
                </div>
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
<script>
    function processWithdrawal(event) {
        event.preventDefault();
        const form = event.target;
        const formData = new FormData(form);

        axios.post('/withdraw', formData)
            .then(response => {
                if (response.data.success) {
                    showStatus('success', 'Withdrawal successful! Funds will be credited to your account.');
                    updateBalance(response.data.new_balance);
                }
            })
            .catch(error => {
                showStatus('error', error.response.data.error);
            });
    }

    function showStatus(type, message) {
        const statusDiv = document.getElementById('withdrawalStatus');
        statusDiv.className = `mt-4 p-4 rounded-lg ${type === 'success' ? 'bg-green-600' : 'bg-info-600'} text-white`;
        statusDiv.textContent = message;
        statusDiv.classList.remove('hidden');
    }

    function updateBalance(newBalance) {
        // Update the balance display in your UI
        const balanceElement = document.querySelector('.balance-amount');
        if (balanceElement) {
            balanceElement.textContent = `₦${newBalance.toFixed(2)}`;
        }
    }
</script>
@endsection