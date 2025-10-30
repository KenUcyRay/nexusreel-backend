<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FoodTransaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function foodTransactions()
    {
        $transactions = FoodTransaction::with(['user', 'items.food'])
            ->orderBy('created_at', 'desc')
            ->get();
        
        return response()->json([
            'success' => true,
            'data' => $transactions
        ]);
    }

    public function foodStats()
    {
        $totalRevenue = FoodTransaction::sum('total_amount');
        $totalTransactions = FoodTransaction::count();
        $todayRevenue = FoodTransaction::whereDate('created_at', today())->sum('total_amount');
        $todayTransactions = FoodTransaction::whereDate('created_at', today())->count();

        return response()->json([
            'success' => true,
            'data' => [
                'totalRevenue' => $totalRevenue,
                'totalTransactions' => $totalTransactions,
                'todayRevenue' => $todayRevenue,
                'todayTransactions' => $todayTransactions
            ]
        ]);
    }

    public function showFoodTransaction($id)
    {
        $transaction = FoodTransaction::with(['user', 'items.food'])->findOrFail($id);
        
        return response()->json([
            'success' => true,
            'data' => $transaction
        ]);
    }
}