class TransactionController extends Controller
{
    public function pending()
    {
        $withdrawals = Transaction::where('type', 'withdrawal')
                                ->where('status', 'pending')
                                ->paginate(20);

        return view('admin.transactions.pending', compact('withdrawals'));
    }

    public function approve(Transaction $transaction)
    {
        $transaction->update(['status' => 'approved']);
        // Process withdrawal logic
        return back();
    }
}
