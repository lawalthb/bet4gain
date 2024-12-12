@extends('admin.layouts.app')

@section('title', 'Manage Admins')
@section('header', 'Admin Management')

@section('content')
<div class="space-y-6">
    <!-- Create New Admin -->
    <div class="bg-white shadow rounded-lg p-6">
        <h3 class="text-lg font-semibold mb-4">Create New Admin</h3>
        <form action="{{ route('admin.admins.store') }}" method="POST" class="space-y-4">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Name</label>
                    <input type="text" name="name" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" style="border: 1px solid #ccc;  font-size: 16px; border-radius: 5px; padding: 5px;">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Email</label>
                    <input type="email" name="email" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" style="border: 1px solid #ccc;  font-size: 16px; border-radius: 5px; padding: 5px;">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Role</label>
                    <select name="role" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" style="border: 1px solid #ccc;  font-size: 16px; border-radius: 5px; padding: 5px;">
                        <option value="admin">Admin</option>
                        <option value="super_admin">Super Admin</option>
                        <option value="game_manager">Game manager</option>
                    </select>
                </div>
            </div>
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                Create Admin
            </button>
        </form>
    </div>

    <!-- Admin List -->
    <div class="bg-white shadow rounded-lg">
        <table class="min-w-full divide-y divide-gray-200">
            <thead>
                <tr>
                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase">Role</th>
                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($admins as $admin)
                <tr>
                    <td class="px-6 py-4">{{ $admin->name }}</td>
                    <td class="px-6 py-4">{{ $admin->email }}</td>
                    <td class="px-6 py-4">{{ ucfirst($admin->role) }}</td>
                    <td class="px-6 py-4">
                        <button onclick="editAdmin({{ $admin->id }})" class="text-blue-600 hover:text-blue-900">Edit</button>
                        @if(auth()->guard('admin')->user()->id !== $admin->id)
                        <form action="{{ route('admin.admins.destroy', $admin->id) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="ml-2 text-red-600 hover:text-red-900">Delete</button>
                        </form>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection