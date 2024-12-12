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
        <div class="w-64 bg-gray-800 text-white">
            <div class="p-4">
                <h1 class="text-2xl font-bold">Admin Panel</h1>
            </div>
            <nav class="mt-4">
                <a href="{{ route('admin.dashboard') }}" class="block p-4 hover:bg-gray-700">Dashboard</a>
                <a href="{{ route('admin.users.index') }}" class="block p-4 hover:bg-gray-700">Users</a>
                <a href="{{ route('admin.transactions') }}" class="block p-4 hover:bg-gray-700">Transactions</a>
                <a href="{{ route('admin.games.crash') }}" class="block p-4 hover:bg-gray-700">Crash Game</a>
                <a href="#" class="block p-4 hover:bg-gray-700">Spin Wheel</a>
                <a href="{{ route('admin.settings') }}" class="block p-4 hover:bg-gray-700">Settings</a>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="flex-1 overflow-x-hidden overflow-y-auto">
            <header class="bg-white shadow">
                <div class="px-4 py-6">
                    <h2 class="text-xl font-semibold">@yield('header')</h2>
                </div>
            </header>

            <main class="p-6">
                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>
