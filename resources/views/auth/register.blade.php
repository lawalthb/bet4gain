@extends('layouts.app')

@section('content')
<div class="max-w-md mx-auto bg-gray-800 p-6 rounded-lg">
    <h2 class="text-2xl font-bold mb-6">Register</h2>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <div class="mb-4">
            <label class="block mb-2">Name</label>
            <input type="text" name="name" class="w-full p-2 rounded bg-gray-700" required>
        </div>

        <div class="mb-4">
            <label class="block mb-2">Email</label>
            <input type="email" name="email" class="w-full p-2 rounded bg-gray-700" required>
        </div>

        <div class="mb-4">
            <label class="block mb-2">Password</label>
            <input type="password" name="password" class="w-full p-2 rounded bg-gray-700" required>
        </div>

        <div class="mb-4">
            <label class="block mb-2">Confirm Password</label>
            <input type="password" name="password_confirmation" class="w-full p-2 rounded bg-gray-700" required>
        </div>

        <button type="submit" class="w-full bg-blue-600 p-2 rounded hover:bg-blue-700">
            Register
        </button>
    </form>
</div>
@endsection
