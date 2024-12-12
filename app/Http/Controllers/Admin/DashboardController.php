class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_users' => User::count(),
            'total_bets' => Bet::count(),
            'total_deposits' => Transaction::where('type', 'deposit')->sum('amount'),
            'total_withdrawals' => Transaction::where('type', 'withdrawal')->sum('amount')
        ];

        return view('admin.dashboard', compact('stats'));
    }
}
