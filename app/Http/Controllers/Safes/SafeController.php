<?php

namespace App\Http\Controllers\Safes;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSafeRequest;
use App\Http\Requests\UpdateSafeRequest;
use App\Http\Requests\DepositRequest;
use App\Http\Requests\WithdrawRequest;
use App\Models\Safe;
use App\Models\SafeTransaction;
use Illuminate\Http\Request;

class SafeController extends Controller
{
    public function index()
    {
        $safes = Safe::with('transactions')->paginate(20);
        $stats = [
            'total_balance' => Safe::sum('balance'),
            'total_safes' => Safe::count(),
            'active_safes' => Safe::where('is_active', true)->count(),
        ];
        return view('safes.index', compact('safes', 'stats'));
    }

    public function create()
    {
        $safe = null;
        return view('safes.form', compact('safe'));
    }

    public function show(Safe $safe)
    {
        $todayDeposits = SafeTransaction::where('safe_id', $safe->id)
            ->where('type', 'deposit')
            ->whereDate('created_at', today())
            ->sum('amount');
        
        $todayWithdrawals = SafeTransaction::where('safe_id', $safe->id)
            ->where('type', 'withdrawal')
            ->whereDate('created_at', today())
            ->sum('amount');
        
        $todayTransactionCount = SafeTransaction::where('safe_id', $safe->id)
            ->whereDate('created_at', today())
            ->count();
        
        $recentTransactions = SafeTransaction::where('safe_id', $safe->id)
            ->latest()
            ->take(10)
            ->get();

        return view('safes.show', compact('safe', 'todayDeposits', 'todayWithdrawals', 'todayTransactionCount', 'recentTransactions'));
    }

    public function store(StoreSafeRequest $request)
    {
        $validated = $request->validated();
        $validated['balance'] = 0;
        Safe::create($validated);

        return redirect()->route('safes.index')->with('success', 'Safe created successfully!');
    }

    public function edit(Safe $safe)
    {
        return view('safes.form', compact('safe'));
    }

    public function update(UpdateSafeRequest $request, Safe $safe)
    {
        $validated = $request->validated();
        $safe->update($validated);
        return redirect()->route('safes.index')->with('success', 'Safe updated successfully!');
    }

    public function destroy(Safe $safe)
    {
        $safe->delete();
        return response()->json(['success' => true]);
    }

    public function deposit(DepositRequest $request, Safe $safe)
    {
        $validated = $request->validated();
        $validated['safe_id'] = $safe->id;
        $validated['type'] = 'deposit';
        $validated['user_id'] = auth()->id();

        SafeTransaction::create($validated);
        $safe->update(['balance' => $safe->balance + $validated['amount']]);

        return redirect()->route('safes.transactions', $safe->id)->with('success', 'Deposit recorded!');
    }

    public function withdraw(WithdrawRequest $request, Safe $safe)
    {
        $validated = $request->validated();

        if ($validated['amount'] > $safe->balance) {
            return back()->withErrors(['amount' => 'Insufficient balance!']);
        }

        $validated['safe_id'] = $safe->id;
        $validated['type'] = 'withdrawal';
        $validated['user_id'] = auth()->id();

        SafeTransaction::create($validated);
        $safe->update(['balance' => $safe->balance - $validated['amount']]);

        return redirect()->route('safes.transactions', $safe->id)->with('success', 'Withdrawal recorded!');
    }

    public function transactions(Safe $safe)
    {
        $transactions = $safe->transactions()->with('user')->paginate(20);
        return view('safes.transactions', compact('safe', 'transactions'));
    }
}
