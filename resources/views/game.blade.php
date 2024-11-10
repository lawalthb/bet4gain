@extends('layouts.app')

@section('content')

<div class="grid grid-cols-3 gap-6">
    <div class="col-span-2">
        <div id="app"></div>
    </div>
    <div class="space-y-6">
        <div class="bg-gray-800 p-4 rounded-lg">
            <h2 class="text-xl font-bold mb-4">Game History</h2>
            <!-- Game history component -->
        </div>
        <div class="bg-gray-800 p-4 rounded-lg">
            <h2 class="text-xl font-bold mb-4">Leaderboard</h2>
            <!-- Leaderboard component -->
        </div>
        <div class="bg-gray-800 p-4 rounded-lg">
            <h2 class="text-xl font-bold mb-4">Chat</h2>
            <!-- Chat component -->
        </div>
    </div>
</div>
@endsection
