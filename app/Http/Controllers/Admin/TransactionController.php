<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FoodTransaction;
use App\Models\MovieTransaction;
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

    public function movieTransactions()
    {
        $transactions = MovieTransaction::with([
            'schedule.movie',
            'schedule.studio'
        ])->orderBy('created_at', 'desc')->get();
        
        $formattedTransactions = $transactions->map(function ($transaction) {
            return [
                'id' => $transaction->id,
                'customer_name' => $transaction->customer_name,
                'customer_email' => $transaction->customer_email,
                'amount' => $transaction->amount,
                'status' => $transaction->status,
                'seats' => $transaction->seats,
                'ticket_count' => $transaction->ticket_count,
                'movie' => [
                    'id' => $transaction->schedule->movie->id,
                    'title' => $transaction->schedule->movie->name
                ],
                'schedule' => [
                    'show_date' => $transaction->schedule->show_date,
                    'show_time' => $transaction->schedule->show_time,
                    'studio' => [
                        'name' => $transaction->schedule->studio->name
                    ]
                ],
                'created_at' => $transaction->created_at
            ];
        });
        
        return response()->json([
            'success' => true,
            'data' => [
                'transactions' => $formattedTransactions
            ]
        ]);
    }

    public function showMovieTransaction($id)
    {
        $transaction = MovieTransaction::with([
            'schedule.movie',
            'schedule.studio'
        ])->findOrFail($id);
        
        $formattedTransaction = [
            'id' => $transaction->id,
            'customer_name' => $transaction->customer_name,
            'customer_email' => $transaction->customer_email,
            'amount' => $transaction->amount,
            'status' => $transaction->status,
            'seats' => $transaction->seats,
            'ticket_count' => $transaction->ticket_count,
            'movie' => [
                'id' => $transaction->schedule->movie->id,
                'title' => $transaction->schedule->movie->name
            ],
            'schedule' => [
                'show_date' => $transaction->schedule->show_date,
                'show_time' => $transaction->schedule->show_time,
                'studio' => [
                    'name' => $transaction->schedule->studio->name
                ]
            ],
            'created_at' => $transaction->created_at
        ];
        
        return response()->json([
            'success' => true,
            'data' => $formattedTransaction
        ]);
    }
}