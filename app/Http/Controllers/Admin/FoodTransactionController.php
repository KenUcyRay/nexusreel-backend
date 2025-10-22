<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FoodTransaction;
use Illuminate\Http\Request;

class FoodTransactionController extends Controller
{
    public function index(Request $request)
    {
        $query = FoodTransaction::with('items');

        // Apply filters
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Pagination
        $limit = $request->get('limit', 10);
        $transactions = $query->orderBy('created_at', 'desc')->paginate($limit);

        // Calculate stats
        $totalRevenue = FoodTransaction::where('status', 'completed')->sum('total_amount');
        $totalTransactions = FoodTransaction::count();
        $todayRevenue = FoodTransaction::where('status', 'completed')
                                     ->whereDate('created_at', today())
                                     ->sum('total_amount');

        return response()->json([
            'success' => true,
            'data' => [
                'transactions' => $transactions->items(),
                'pagination' => [
                    'current_page' => $transactions->currentPage(),
                    'total_pages' => $transactions->lastPage(),
                    'total_items' => $transactions->total(),
                ],
                'stats' => [
                    'total_revenue' => $totalRevenue,
                    'total_transactions' => $totalTransactions,
                    'today_revenue' => $todayRevenue,
                ]
            ]
        ]);
    }

    public function show($id)
    {
        $transaction = FoodTransaction::with('items')->find($id);

        if (!$transaction) {
            return response()->json([
                'success' => false,
                'message' => 'Transaction not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $transaction
        ]);
    }

    public function dashboard()
    {
        $totalFoodItems = \App\Models\Food::where('is_active', true)->count();
        $totalFoodRevenue = FoodTransaction::where('status', 'completed')->sum('total_amount');
        $totalFoodTransactions = FoodTransaction::count();
        $todayFoodRevenue = FoodTransaction::where('status', 'completed')
                                         ->whereDate('created_at', today())
                                         ->sum('total_amount');

        // Popular items
        $popularItems = \App\Models\FoodTransactionItem::select('food_name')
            ->selectRaw('SUM(quantity) as total_sold')
            ->selectRaw('SUM(subtotal) as revenue')
            ->groupBy('food_name')
            ->orderBy('total_sold', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($item) {
                return [
                    'name' => $item->food_name,
                    'total_sold' => $item->total_sold,
                    'revenue' => $item->revenue,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => [
                'total_food_items' => $totalFoodItems,
                'total_food_revenue' => $totalFoodRevenue,
                'total_food_transactions' => $totalFoodTransactions,
                'today_food_revenue' => $todayFoodRevenue,
                'popular_items' => $popularItems,
            ]
        ]);
    }
}