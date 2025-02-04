@extends('admin.layouts.app')

@section('title', 'Transactions')
@section('header', 'All Transactions')


<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">


@section('content')
<div class="space-y-6">
    <!-- Charts Section -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Transaction Volume Chart -->
        <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="text-lg font-semibold mb-4">Transaction Volume</h3>
            <canvas id="volumeChart"></canvas>
        </div>

        <!-- Transaction Status Distribution -->
        <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="text-lg font-semibold mb-4">Status Distribution</h3>
            <canvas id="statusChart"></canvas>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="bg-white p-6 rounded-lg shadow">
        <form action="{{ route('admin.transactions.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Type</label>
                <select name="type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                    <option value="">All Types</option>
                    <option value="deposit" {{ request('type') === 'deposit' ? 'selected' : '' }}>Deposit</option>
                    <option value="withdrawal" {{ request('type') === 'withdrawal' ? 'selected' : '' }}>Withdrawal</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Status</label>
                <select name="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                    <option value="">All Status</option>
                    <option value="Success" {{ request('status') === 'Success' ? 'selected' : '' }}>Success</option>
                    <option value="Pending" {{ request('status') === 'Pending' ? 'selected' : '' }}>Pending</option>
                    <option value="Failed" {{ request('status') === 'Failed' ? 'selected' : '' }}>Failed</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Date Range</label>
                <input type="text" name="date_range" id="date_range" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" placeholder="Select date range">
            </div>

            <div class="flex items-end">
                <button type="submit" class="w-full bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                    Apply Filters
                </button>
            </div>
        </form>
    </div>
   <!-- Total Amount Card -->
    <div class="bg-white p-6 rounded-lg shadow">
        <h3 class="text-xl font-bold">Total Amount: ₦{{ number_format($totalAmount, 2) }}</h3>
    </div>
    <!-- Transactions Table -->
    <div class="bg-white shadow rounded-lg">
        <table class="min-w-full divide-y divide-gray-200">
            <thead>
                <tr>
                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase">User</th>
                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($transactions as $transaction)
                <tr>
                    <td class="px-6 py-4">{{ $transaction->id }}</td>
                    <td class="px-6 py-4">{{ $transaction->user->name }}</td>
                    <td class="px-6 py-4 capitalize">{{ $transaction->type }}</td>
                    <td class="px-6 py-4">₦{{ number_format($transaction->amount, 2) }}</td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 text-xs rounded-full
                            {{ $transaction->status === 'Success' ? 'bg-green-100 text-green-800' :
                               ($transaction->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                            {{ ucfirst($transaction->status) }}
                        </span>
                    </td>
                    <td class="px-6 py-4">{{ $transaction->created_at->format('M d, Y H:i') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="p-4">
            {{ $transactions->withQueryString()->links() }}
        </div>
    </div>
</div>


<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
    // Initialize date picker
    flatpickr("#date_range", {
        mode: "range",
        dateFormat: "Y-m-d",
    });

    // Volume Chart
    const volumeData = @json($chartData['daily']);
    new Chart(document.getElementById('volumeChart'), {
        type: 'line',
        data: {
            labels: Object.keys(volumeData).map(date => date),
            datasets: [
                {
                    label: 'Total Transactions',
                    data: Object.values(volumeData).map(value => value),
                    borderColor: '#10B981',
                    fill: false
                }
            ]
        }
    });

    // Status Distribution Chart
    new Chart(document.getElementById('statusChart'), {
        type: 'doughnut',
        data: {
            labels: @json($chartData['status']->pluck('status')),
            datasets: [{
                data: @json($chartData['status']->pluck('count')),
                backgroundColor: ['#10B981', '#F59E0B', '#EF4444']
            }]
        }
    });
</script>

@endsection
