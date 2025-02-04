@extends('layouts.app')

@section('content')
<div class="min-h-screen flex items-center justify-center">
    <div class="max-w-md  rounded-lg shadow" style="margin-top: -300px;">
        <div class="text-center">
            <img src="{{ asset('assets/images/bet4gain-preload.png') }}"
                 alt="Bet4Gain"
                 class="mx-auto h-20 w-auto"
            >
            <h2 class="mt-6 text-3xl font-extrabold text-gray-900">Sign in to your account</h2>
        </div>
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
</div>
@endsection
