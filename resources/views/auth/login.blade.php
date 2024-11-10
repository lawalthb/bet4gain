@extends('layouts.app')

@section('content')
<div class="max-w-md mx-auto bg-gray-800 p-6 rounded-lg">
    <h2 class="text-2xl font-bold mb-6">Login</h2>

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="mb-4">
            <label class="block mb-2">Email</label>
            <input type="email" name="email" class="w-full p-2 rounded bg-gray-700" required>
        </div>

        <div class="mb-4">
            <label class="block mb-2">Password</label>
            <input type="password" name="password" class="w-full p-2 rounded bg-gray-700" required>
        </div>

        <button type="submit" class="w-full bg-blue-600 p-2 rounded hover:bg-blue-700">
            Login
        </button>
    </form>
</div>
@endsection
