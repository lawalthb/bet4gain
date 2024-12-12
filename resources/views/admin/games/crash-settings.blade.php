@extends('admin.layouts.app')

@section('title', 'Crash Game Settings')
@section('header', 'Crash Game Configuration')

@section('content')
<div class="bg-white shadow rounded-lg p-6">
    <form action="{{ route('admin.games.crash.update') }}" method="POST">
        @csrf
        <div class="space-y-6">
            <div>
                <label class="block text-sm font-medium text-gray-700">Minimum Bet</label>
                <input type="number" name="min_bet" value="{{ $settings['min_bet'] }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Maximum Bet</label>
                <input type="number" name="max_bet" value="{{ $settings['max_bet'] }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">House Edge (%)</label>
                <input type="number" name="house_edge" value="{{ $settings['house_edge'] }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
            </div>
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                Save Settings
            </button>
        </div>
    </form>
</div>
@endsection
