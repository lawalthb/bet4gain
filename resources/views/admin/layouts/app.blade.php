<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - @yield('title')</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="flex h-screen">
        <!-- Sidebar -->
        @if(auth()->guard('admin')->check())
            <div class="w-64 bg-gray-800 text-white">
                <div class="p-4">
                    <h1 class="text-2xl font-bold">Admin Panel</h1>
                </div>
                <nav class="mt-4">
                    <a href="{{ route('admin.dashboard') }}" class="block p-4 hover:bg-gray-700">Dashboard</a>
                    <a href="{{ route('admin.users.index') }}" class="block p-4 hover:bg-gray-700">Users</a>
                    <a href="{{ route('admin.admins.index') }}" class="block p-4 hover:bg-gray-700">Manage Admins</a>
                    <a href="{{ route('admin.transactions') }}" class="block p-4 hover:bg-gray-700">Transactions</a>
                    <a href="{{ route('admin.games.crash') }}" class="block p-4 hover:bg-gray-700">Crash Game</a>
                    <a href="{{ route('admin.games.spin') }}" class="block p-4 hover:bg-gray-700">Spin Wheel</a>
                    <a href="{{ route('admin.settings') }}" class="block p-4 hover:bg-gray-700">Settings</a>
                </nav>
            </div>
        @endif
        <!-- Main Content -->
        <div class="flex-1 flex flex-col">
            <!-- Top Navigation -->
            <div class="bg-white shadow">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between h-16">
                        <div class="flex">
                            <div class="flex-shrink-0 flex items-center">
                                <h2 class="text-xl font-semibold">@yield('header')</h2>
                            </div>
                        </div>
                        <div class="flex items-center">
                            <div class="relative" x-data="{ open: false }">
                                @if(auth()->guard('admin')->check())
                                    <button @click="open = !open" class="flex items-center space-x-2 text-gray-700 hover:text-gray-900">
                                        <span>{{ auth()->guard('admin')->user()->name }}</span>
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                        </svg>
                                    </button>
                                    <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-48 rounded-md shadow-lg py-1 bg-white ring-1 ring-black ring-opacity-5">
                                        <a href="{{ route('admin.profile') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Profile</a>
                                        <form method="POST" action="{{ route('admin.logout') }}">
                                            @csrf
                                            <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Logout</button>
                                        </form>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <main class="flex-1 overflow-x-hidden overflow-y-auto p-6">
                @yield('content')
            </main>
        </div>
    </div>
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</body>
</html>
