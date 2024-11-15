@extends('layouts.app')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 md:gap-6 p-4">
    <div class="col-span-1 md:col-span-2">
        <div id="app"></div>
    </div>
    <div class="space-y-4 md:space-y-6">
        <div class="bg-gray-800 p-4 rounded-lg">
            <h2 class="text-xl font-bold mb-4">Game History</h2>
            <div id="game-history"></div>
        </div>
        <div class="bg-gray-800 p-4 rounded-lg">
            <h2 class="text-xl font-bold mb-4">Leaderboard</h2>
            <div id="leader-board"></div>
        </div>
        <div class="bg-gray-800 p-4 rounded-lg">
            <h2 class="text-xl font-bold mb-4">Chat</h2>
            <div id="chat"></div>
        </div>
    </div>
</div>
@endsection
