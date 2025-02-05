@extends('admin.layouts.app')

@section('title', 'Crash Game Management')
@section('header', 'Crash Game Management')

@section('content')
<div class="space-y-6">
    <!-- Game Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="text-sm font-medium text-gray-500">Total Games</h3>
            <p class="text-2xl font-bold">{{ number_format($statistics['total_games']) }}</p>
        </div>
        <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="text-sm font-medium text-gray-500">Total Bets</h3>
            <p class="text-2xl font-bold">₦{{ number_format($statistics['total_bets']) }}</p>
        </div>
        <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="text-sm font-medium text-gray-500">Average Crash Point</h3>
            <p class="text-2xl font-bold">{{ number_format($statistics['average_crash_point'], 2) }}x</p>
        </div>
        <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="text-sm font-medium text-gray-500">Highest Crash Point</h3>
            <p class="text-2xl font-bold">{{ number_format($statistics['highest_crash_point'], 2) }}x</p>
        </div>
    </div>

    <!-- Game Settings -->
    <div class="bg-white shadow rounded-lg p-6">
        <h2 class="text-lg font-semibold mb-4">Game Settings</h2>
        <form action="{{ route('admin.games.crash.update') }}" method="POST" class="space-y-4">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Minimum Bet</label>
                    <input type="number" name="min_bet" value="{{ $settings['min_bet'] }}"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Maximum Bet</label>
                    <input type="number" name="max_bet" value="{{ $settings['max_bet'] }}"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">House Edge (%)</label>
                    <input type="number" name="house_edge" value="{{ $settings['house_edge'] }}"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Maximum Multiplier</label>
                    <input type="number" name="max_multiplier" value="{{ $settings['max_multiplier'] }}"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                </div>
            </div>
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                Save Settings
            </button>
        </form>
    </div>

    <!-- Recent Games -->
    <div class="bg-white shadow rounded-lg p-6">
        <h2 class="text-lg font-semibold mb-4">Recent Games</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead>
                    <tr>
                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase">Game ID</th>
                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase">Crash Point</th>
                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase">Total Bets</th>
                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase">Players</th>

                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase">Status</th>


                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($recentGames as $game)
                    <tr>
                        <td class="px-6 py-4">{{ $game->id }}</td>
                        <td class="px-6 py-4">{{ number_format($game->crash_point, 2) }}x</td>
                        <td class="px-6 py-4">₦{{ number_format($game->total_bets) }}</td>
                        <td class="px-6 py-4">{{ $game->bets->count() }}</td>
                        <td class="px-6 py-4"><span class="px-2 py-1 text-xs rounded-full {{ $game->is_completed ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}">
    {{ $game->is_completed ? 'Completed' : 'Active' }}
</span></td>
                        <td class="px-6 py-4">{{ $game->created_at->format('M d, Y H:i') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
