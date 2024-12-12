<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
class UserController extends Controller
{
    public function index()
    {
        $users = User::paginate(20);
        return view('admin.users.index', compact('users'));
    }

    public function ban(User $user)
    {
        $user->update(['status' => 'banned']);
        return back()->with('success', 'User banned');
    }

    public function transactions(User $user)
    {
        $transactions = $user->transactions()->paginate(20);
        return view('admin.users.transactions', compact('transactions'));
    }
}
