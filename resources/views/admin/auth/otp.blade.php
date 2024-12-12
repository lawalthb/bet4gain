@extends('admin.layouts.app')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-100">
    <div class="max-w-md w-full space-y-8 p-8 bg-white rounded-lg shadow">
        <div>
            <h2 class="text-center text-3xl font-extrabold text-gray-900">Enter OTP</h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                Please enter the OTP sent to your email
            </p>
        </div>
        <form class="mt-8 space-y-6" action="{{ route('admin.otp.verify') }}" method="POST">
            @csrf
            <div>
                <label for="otp" class="block text-sm font-medium text-gray-700">OTP Code</label>
                <input type="text" name="otp" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" placeholder="Enter 6-digit OTP" style="border: 1px solid #ccc;  font-size: 16px; border-radius: 5px; padding: 5px;">
            </div>
            <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                Verify OTP
            </button>
        </form>
    </div>
</div>
@endsection