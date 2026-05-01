<?php

namespace App\Http\Controllers\Api;

use App\Models\ChartOfAccount;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;

class ChartOfAccountController extends Controller
{
    /**
     * Get all accounts with hierarchical structure
     */
    public function index(Request $request): JsonResponse
    {
        $type = $request->get('type');
        $active = $request->get('active', true);

        $query = ChartOfAccount::query();

        if ($type) {
            $query->where('account_type', $type);
        }

        if ($active) {
            $query->where('is_active', true);
        }

        $accounts = $query->whereNull('parent_account_id')
            ->with('children')
            ->orderBy('account_code')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $accounts,
        ]);
    }

    /**
     * Get a single account
     */
    public function show(ChartOfAccount $account): JsonResponse
    {
        $startDate = request()->get('start_date');
        $endDate = request()->get('end_date');

        $balance = $account->getBalance($startDate, $endDate);

        return response()->json([
            'success' => true,
            'data' => $account->load('parent', 'children'),
            'balance' => $balance,
        ]);
    }

    /**
     * Create a new account
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'account_code' => 'required|string|unique:chart_of_accounts|max:20',
            'account_name' => 'required|string|max:255',
            'account_name_ar' => 'required|string|max:255',
            'account_type' => 'required|in:asset,liability,equity,revenue,expense',
            'parent_account_id' => 'nullable|exists:chart_of_accounts,id',
            'description' => 'nullable|string',
            'opening_balance' => 'nullable|numeric',
            'is_active' => 'boolean',
        ]);

        $account = ChartOfAccount::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Account created successfully',
            'data' => $account,
        ], 201);
    }

    /**
     * Update an account
     */
    public function update(Request $request, ChartOfAccount $account): JsonResponse
    {
        $validated = $request->validate([
            'account_code' => 'string|unique:chart_of_accounts,account_code,' . $account->id . '|max:20',
            'account_name' => 'string|max:255',
            'account_name_ar' => 'string|max:255',
            'account_type' => 'in:asset,liability,equity,revenue,expense',
            'parent_account_id' => 'nullable|exists:chart_of_accounts,id',
            'description' => 'nullable|string',
            'opening_balance' => 'nullable|numeric',
            'is_active' => 'boolean',
        ]);

        $account->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Account updated successfully',
            'data' => $account,
        ]);
    }

    /**
     * Get account balance for a period
     */
    public function balance(ChartOfAccount $account, Request $request): JsonResponse
    {
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        $balance = $account->getBalance($startDate, $endDate);

        return response()->json([
            'success' => true,
            'account' => $account,
            'balance' => $balance,
            'opening_balance' => $account->opening_balance,
        ]);
    }

    /**
     * Get accounts by type
     */
    public function byType($type): JsonResponse
    {
        $accounts = ChartOfAccount::byType($type)->active()->get();

        return response()->json([
            'success' => true,
            'type' => $type,
            'data' => $accounts,
        ]);
    }
}
