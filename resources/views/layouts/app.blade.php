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

        .nav-menu {
            transition: all 0.3s ease;
        }

        .hamburger {
            display: none;
            cursor: pointer;
            padding: 10px;
        }

        @media (max-width: 768px) {
            .hamburger {
                display: block;
            }

            .nav-menu {
                display: none;
                width: 100%;
            }

            .nav-menu.active {
                display: flex;
            }
        }
    </style>
</head>

<body class="bg-gray-900 text-white">
    <x-preloader />
    <nav class="bg-gray-800 p-4">
        <div class="container mx-auto">
            <div class="flex justify-between items-center">
                <a href="/" class="text-xl font-bold relative">
                    <span class="bg-clip-text text-transparent bg-gradient-to-r from-green-400 via-blue-500 to-purple-500 hover:from-pink-500 hover:via-yellow-500 hover:to-green-500 transition-all duration-300">
                        <img src="{{ asset('assets/images/favicon.png') }}" alt="Bet4Gain" class="w-10 h-10 inline-block mr-2"> Bet4Gain
                    </span>
                </a>

                <!-- Navigation Links for Desktop -->
                <div class="hidden md:flex items-center space-x-4 justify-end">
                    @auth
                    <a href="{{ route('wallet') }}" class="hover:text-gray-300">Wallet</a>

                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="hover:text-gray-300">Logout</button>
                    </form>
                    <span>
                        <a href="{{ route('player.profile') }}" class="hover:text-green-400 transition-colors duration-200 flex items-center">
                            {{ auth()->user()->name }}
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </a>
                    </span>
                    @else
                    <a href="{{ route('login') }}" class="hover:text-gray-300">Login</a>
                    <a href="{{ route('register') }}" class="hover:text-gray-300">Register</a>
                    @endauth
                </div>

                <!-- Hamburger Menu for Mobile -->
                <button onclick="toggleMenu()" class="hamburger md:hidden">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-16 6h16" />
                    </svg>
                </button>
            </div>

            <!-- Mobile Menu -->
            <div class="nav-menu md:hidden" id="navMenu">
                <div class="flex flex-col space-y-4 py-4">
                    @auth
                    <a href="{{ route('wallet') }}" class="hover:text-gray-300">Wallet</a>

                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="hover:text-gray-300">Logout</button>
                    </form>
                    <span>
                        <a href="{{ route('player.profile') }}" class="hover:text-green-400 transition-colors duration-200 flex items-center">
                            {{ auth()->user()->name }}
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </a>
                    </span>
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

<script>
    window.auth = {
        user: @json(auth() -> user()),
        isLoggedIn: @json(auth() -> check())
    }

    function toggleMenu() {
        const menu = document.getElementById('navMenu');
        menu.classList.toggle('active');
    }
</script>

</html>
