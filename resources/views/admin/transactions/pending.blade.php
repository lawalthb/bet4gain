@extends('admin.layouts.app')

@section('title', 'Pending Transactions')
@section('header', 'Pending Withdrawals')

@section('content')
<div class="bg-white shadow rounded-lg">
    <table class="min-w-full divide-y divide-gray-200">
        <thead>
            <tr>
                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($withdrawals as $withdrawal)
            <tr>
                <td class="px-6 py-4">{{ $withdrawal->id }}</td>
                <td class="px-6 py-4">{{ $withdrawal->user->name }}</td>
                <td class="px-6 py-4">${{ number_format($withdrawal->amount, 2) }}</td>
                <td class="px-6 py-4">{{ $withdrawal->type }}</td>
                <td class="px-6 py-4">
                    <form action="{{ route('admin.transactions.approve', $withdrawal->id) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="text-green-600 hover:text-green-900">Approve</button>
                    </form>
                    <form action="{{ route('admin.transactions.reject', $withdrawal->id) }}" method="POST" class="inline ml-2">
                        @csrf
                        <button type="submit" class="text-red-600 hover:text-red-900">Reject</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    {{ $withdrawals->links() }}
</div>
@endsection
