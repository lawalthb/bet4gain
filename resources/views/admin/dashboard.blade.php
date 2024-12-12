@extends('admin.layouts.app')

@section('title', 'Dashboard')
@section('header', 'Dashboard Overview')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
    <div class="bg-white p-6 rounded-lg shadow">
        <h3 class="text-lg font-semibold">Total Users</h3>
        <p class="text-3xl font-bold">{{ $stats['total_users'] }}</p>
        <p class="text-sm text-gray-500">↑ {{ $stats['new_users_today'] }} today</p>
    </div>
    <div class="bg-white p-6 rounded-lg shadow">
        <h3 class="text-lg font-semibold">Total Bets</h3>
        <p class="text-3xl font-bold">{{ $stats['total_bets'] }}</p>
        <p class="text-sm text-gray-500">↑ {{ $stats['bets_today'] }} today</p>
    </div>
    <div class="bg-white p-6 rounded-lg shadow">
        <h3 class="text-lg font-semibold">Total Deposits</h3>
        <p class="text-3xl font-bold">₦{{ number_format($stats['total_deposits'], 2) }}</p>
        <p class="text-sm text-green-500">↑ ₦{{ number_format($stats['deposits_today'], 2) }} today</p>
    </div>
    <div class="bg-white p-6 rounded-lg shadow">
        <h3 class="text-lg font-semibold">Total Withdrawals</h3>
        <p class="text-3xl font-bold">₦{{ number_format($stats['total_withdrawals'], 2) }}</p>
        <p class="text-sm text-red-500">↑ ₦{{ number_format($stats['withdrawals_today'], 2) }} today</p>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <!-- Online Users -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold mb-4">Online Players ({{ count($online_users) }})</h3>
        <div class="space-y-3">
            @foreach($online_users as $user)
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <span class="h-2 w-2 bg-green-500 rounded-full mr-2"></span>
                    <span>{{ $user->name }}</span>
                </div>
                <span class="text-sm text-gray-500">₦{{ number_format($user->wallet_balance, 2) }}</span>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Recent Transactions -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold mb-4">Recent Transactions</h3>
        <div class="space-y-3">
            @foreach($recent_transactions as $transaction)
            <div class="flex items-center justify-between">
                <div>
                    <p class="font-medium">{{ $transaction->user->name }}</p>
                    <p class="text-sm text-gray-500">{{ $transaction->type }}</p>
                </div>
                <div class="text-right">
                    <p class="font-medium">₦{{ number_format($transaction->amount, 2) }}</p>
                    <p class="text-sm text-gray-500">{{ $transaction->created_at->diffForHumans() }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Recent Games -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold mb-4">Recent Games</h3>
        <div class="space-y-3">
            @foreach($recent_games as $game)
            <div class="flex items-center justify-between">
                <div>
                    <p class="font-medium">Game #{{ $game->id }}</p>
                    <p class="text-sm text-gray-500">{{ $game->created_at->diffForHumans() }}</p>
                </div>
                <div class="text-right">
                    <p class="font-medium">{{ $game->crash_point }}x</p>
                    <p class="text-sm {{ $game->total_profit >= 0 ? 'text-green-500' : 'text-red-500' }}">
                        ₦{{ number_format($game->total_profit, 2) }}
                    </p>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Recent Users -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold mb-4">New Players</h3>
        <div class="space-y-3">
            @foreach($recent_users as $user)
            <div class="flex items-center justify-between">
                <div>
                    <p class="font-medium">{{ $user->name }}</p>
                    <p class="text-sm text-gray-500">{{ $user->email }}</p>
                </div>
                <div class="text-right">
                    <p class="text-sm text-gray-500">Joined {{ $user->created_at->diffForHumans() }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection