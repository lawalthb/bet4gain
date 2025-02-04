@extends('layouts.app')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 md:gap-6 p-4 w-full max-w-full">

    <div class="col-span-1 md:col-span-2">
        <div id="app"></div>
    </div>
    <div class="space-y-4 md:space-y-6">
        <div class="bg-gray-800 rounded-lg">
            <!-- Tab Navigation -->
            <div class="flex border-b border-gray-700">
                <button onclick="switchTab('history')" class="tab-btn active px-4 py-2" id="historyTab">
                    Game History
                </button>
                <button onclick="switchTab('leaderboard')" class="tab-btn px-4 py-2" id="leaderboardTab">
                    Leaderboard
                </button>
                <button onclick="switchTab('chat')" class="tab-btn px-4 py-2" id="chatTab">
                    Public Chat
                </button>
            </div>

            <!-- Tab Content -->
            <div class="p-4">
                <div id="history" class="tab-content">
<history></history>
                </div>
                <div id="leaderboard" class="tab-content hidden">
<leaderboard></leaderboard>
                </div>
                <div id="chat" class="tab-content hidden">

                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function switchTab(tabName) {
        // Hide all tab content
        document.querySelectorAll('.tab-content').forEach(tab => {
            tab.classList.add('hidden');
        });

        // Remove active class from all tabs
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.classList.remove('active');
        });

        // Show selected tab content
        document.getElementById(tabName).classList.remove('hidden');

        // Add active class to clicked tab
        document.getElementById(tabName + 'Tab').classList.add('active');
    }
</script>

<style>
    .tab-btn {
        color: #888;
        transition: all 0.3s ease;
    }

    .tab-btn:hover {
        color: white;
        background: rgba(255, 255, 255, 0.1);
    }

    .tab-btn.active {
        color: white;
        border-bottom: 2px solid #4CAF50;
    }

    .tab-content {
        transition: all 0.3s ease;
    }
</style>
@endsection
