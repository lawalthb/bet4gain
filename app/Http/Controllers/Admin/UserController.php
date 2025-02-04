<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        // Add search functionality
        if ($request->search) {
            $query->where('name', 'like', "%{$request->search}%")
                  ->orWhere('email', 'like', "%{$request->search}%");
        }

        // Add sorting
        $sort = $request->sort ?? 'created_at';
        $direction = $request->direction ?? 'desc';
        $query->orderBy($sort, $direction);

        $users = $query->paginate(20);
        return view('admin.users.index', compact('users'));
    }

    public function ban(User $user)
    {
        $user->update(['is_ban' => 'Yes']);
        return back()->with('success', 'User banned successfully');
    }

    public function unban(User $user)
    {
        $user->update(['is_ban' => 'No']);
        return back()->with('success', 'User unbanned successfully');
    }

    public function transactions(User $user)
    {
        $transactions = $user->transactions()->latest()->paginate(20);
        return view('admin.users.transactions', compact('user', 'transactions'));
    }
}
