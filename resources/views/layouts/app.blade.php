<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bet4Gain</title>
    <link rel="icon" href="{{ asset('assets/images/favicon.png') }}" type="image/x-icon">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .bg-gradient-text {
            background-size: 200% auto;
            animation: shine 3s linear infinite;
        }

        @keyframes shine {
            to {
                background-position: 200% center;
            }
        }
    </style>
</head>

<body class="bg-gray-900 text-white">
    <x-preloader />
    <nav class="bg-gray-800 p-4">
        <div class="container mx-auto flex justify-between items-center">
            <a href="/" class="text-xl font-bold relative">
                <span class="bg-clip-text text-transparent bg-gradient-to-r from-green-400 via-blue-500 to-purple-500 hover:from-pink-500 hover:via-yellow-500 hover:to-green-500 transition-all duration-300">
                    <img src="{{ asset('assets/images/favicon.png') }}" alt="Bet4Gain" class="w-10 h-10 inline-block mr-2"> Bet4Gain
                </span>
            </a>
            <div class="flex items-center space-x-4">
                @auth
                <a href="{{ route('wallet') }}" class="hover:text-gray-300">Wallet</a>
                <a href="{{ route('spin') }}" class="hover:text-gray-300">Spin Wheel</a>
                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit" class="hover:text-gray-300">Logout</button>
                </form>
                <span>{{ auth()->user()->name }}</span>
                @else
                <a href="{{ route('login') }}" class="hover:text-gray-300">Login</a>
                <a href="{{ route('register') }}" class="hover:text-gray-300">Register</a>
                @endauth
            </div>
        </div>
    </nav>

    <main class="container mx-auto py-6">
        @yield('content')


        <x-footer />
    </main>
</body>

</html>


<script>
    window.auth = {
        user: @json(auth() -> user()),
        isLoggedIn: @json(auth() -> check())
    }
</script>