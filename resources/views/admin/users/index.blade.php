@extends('admin.layouts.app')

@section('title', 'Users')
@section('header', 'Manage Users')

@section('content')
<div class="bg-white shadow rounded-lg">
    <!-- Search and Filter Section -->
    <div class="p-4 border-b border-gray-200">
        <form action="{{ route('admin.users.index') }}" method="GET" class="flex gap-4">
            <div class="flex-1">
                <input type="search"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Search users..."
                    class="w-full px-4 py-2 border rounded-lg">
            </div>
            <div>
                <select name="sort" class="px-4 py-2 border rounded-lg">
                    <option value="created_at" {{ request('sort') === 'created_at' ? 'selected' : '' }}>Join Date</option>
                    <option value="wallet_balance" {{ request('sort') === 'wallet_balance' ? 'selected' : '' }}>Balance</option>
                </select>
            </div>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                Filter
            </button>
        </form>
    </div>

    <!-- Users Table -->
    <table class="min-w-full divide-y divide-gray-200">
        <thead>
            <tr>
                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase">Balance</th>
                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase">Join Date</th>
                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @foreach($users as $user)
            <tr class="{{ $user->is_ban === 'Yes' ? 'bg-red-50' : '' }}">
                <td class="px-6 py-4">{{ $user->id }}</td>
                <td class="px-6 py-4">{{ $user->name }}</td>
                <td class="px-6 py-4">{{ $user->email }}</td>
                <td class="px-6 py-4">₦{{ number_format($user->wallet_balance, 2) }}</td>
                <td class="px-6 py-4">{{ $user->created_at->format('M d, Y') }}</td>
                <td class="px-6 py-4">
                    <span class="px-2 py-1 text-sm rounded-full {{ $user->is_ban === 'Yes' ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }}">
                        {{ $user->is_ban === 'Yes' ? 'Banned' : 'Active' }}
                    </span>
                </td>
                <td class="px-6 py-4 space-x-2">
                    <a href="{{ route('admin.users.transactions', $user->id) }}"
                       class="inline-block px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700">
                        Transactions
                    </a>
                    @if($user->is_ban === 'No')
                        <form action="{{ route('admin.users.ban', $user->id) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700">
                                Ban
                            </button>
                        </form>
                    @else
                        <form action="{{ route('admin.users.unban', $user->id) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="px-3 py-1 bg-green-600 text-white rounded hover:bg-green-700">
                                Unban
                            </button>
                        </form>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="p-4">
        {{ $users->withQueryString()->links() }}
    </div>
</div>
@endsection
