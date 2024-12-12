@extends('admin.layouts.app')

@section('title', 'System Settings')
@section('header', 'System Settings')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <!-- General Settings -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold mb-4">General Settings</h3>
        <form action="{{ route('admin.settings.update') }}" method="POST">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Minimum Withdrawal</label>
                    <input type="number" name="min_withdrawal" value="{{ $settings['general']['min_withdrawal'] }}"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Maximum Withdrawal</label>
                    <input type="number" name="max_withdrawal" value="{{ $settings['general']['max_withdrawal'] }}"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Maintenance Mode</label>
                    <select name="maintenance_mode" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        <option value="0" {{ !$settings['general']['maintenance_mode'] ? 'selected' : '' }}>Off</option>
                        <option value="1" {{ $settings['general']['maintenance_mode'] ? 'selected' : '' }}>On</option>
                    </select>
                </div>
            </div>
            <div class="mt-4">
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                    Save Settings
                </button>
            </div>
        </form>
    </div>

    <!-- Crash Game Settings -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold mb-4">Crash Game Settings</h3>
        <form action="{{ route('admin.games.crash.update') }}" method="POST">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Minimum Bet</label>
                    <input type="number" name="min_bet" value="{{ $settings['crash_game']['min_bet'] }}"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Maximum Bet</label>
                    <input type="number" name="max_bet" value="{{ $settings['crash_game']['max_bet'] }}"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">House Edge (%)</label>
                    <input type="number" name="house_edge" value="{{ $settings['crash_game']['house_edge'] }}"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Maximum Multiplier</label>
                    <input type="number" name="max_multiplier" value="{{ $settings['crash_game']['max_multiplier'] }}"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                </div>
            </div>
            <div class="mt-4">
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                    Save Crash Settings
                </button>
            </div>
        </form>
    </div>

    <!-- Spin Wheel Settings -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold mb-4">Spin Wheel Settings</h3>
        <form action="{{ route('admin.games.spin.update') }}" method="POST">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Minimum Bet</label>
                    <input type="number" name="min_bet" value="{{ $settings['spin_wheel']['min_bet'] }}"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Maximum Bet</label>
                    <input type="number" name="max_bet" value="{{ $settings['spin_wheel']['max_bet'] }}"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                </div>
            </div>
            <div class="mt-4">
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                    Save Spin Settings
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
