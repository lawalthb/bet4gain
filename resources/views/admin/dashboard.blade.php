@extends('admin.layouts.app')

@section('title', 'Dashboard')
@section('header', 'Dashboard Overview')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-4 gap-4">
    <div class="bg-white p-6 rounded-lg shadow">
        <h3 class="text-lg font-semibold">Total Users</h3>
        <p class="text-3xl font-bold">{{ $stats['total_users'] }}</p>
    </div>
    <div class="bg-white p-6 rounded-lg shadow">
        <h3 class="text-lg font-semibold">Total Bets</h3>
        <p class="text-3xl font-bold">{{ $stats['total_bets'] }}</p>
    </div>
    <div class="bg-white p-6 rounded-lg shadow">
        <h3 class="text-lg font-semibold">Total Deposits</h3>
        <p class="text-3xl font-bold">${{ number_format($stats['total_deposits'], 2) }}</p>
    </div>
    <div class="bg-white p-6 rounded-lg shadow">
        <h3 class="text-lg font-semibold">Total Withdrawals</h3>
        <p class="text-3xl font-bold">${{ number_format($stats['total_withdrawals'], 2) }}</p>
    </div>
</div>
@endsection
