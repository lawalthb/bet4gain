<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crash Game</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-900 text-white">
    <nav class="bg-gray-800 p-4">
        <div class="container mx-auto flex justify-between items-center">
            <a href="/" class="text-xl font-bold">Crash Game</a>
            <div class="flex items-center space-x-4">
                @auth

                <a href="{{ route('wallet') }}" class="hover:text-gray-300">Wallet</a>
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

<script src="https://js.paystack.co/v1/inline.js"></script>

<script>
    window.auth = {
        user: @json(auth() -> user()),
        isLoggedIn: @json(auth() -> check())
    }
</script>
