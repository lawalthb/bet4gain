<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bet;
use App\Models\Game;
use App\Models\Transaction;
use App\Models\User;

class DashboardController extends Controller
{
public function index()
{
    $stats = [
        'total_users' => User::count(),
        'new_users_today' => User::whereDate('created_at', today())->count(),
        'total_bets' => Bet::count(),
        'bets_today' => Bet::whereDate('created_at', today())->count(),
        'total_deposits' => Transaction::where('type', 'deposit')->sum('amount'),
        'deposits_today' => Transaction::where('type', 'deposit')->whereDate('created_at', today())->sum('amount'),
        'total_withdrawals' => Transaction::where('type', 'withdrawal')->sum('amount'),
        'withdrawals_today' => Transaction::where('type', 'withdrawal')->whereDate('created_at', today())->sum('amount'),
    ];

    $online_users = User::where('last_seen_at', '>=', now()->subMinutes(5))->get();
    $recent_transactions = Transaction::with('user')->latest()->take(5)->get();
    $recent_games = Game::latest()->take(5)->get();
    $recent_users = User::latest()->take(5)->get();

    return view('admin.dashboard', compact('stats', 'online_users', 'recent_transactions', 'recent_games', 'recent_users'));
}    }

