<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FoodTransaction;
use App\Models\Food;
use Midtrans\Config;
use Midtrans\Snap;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class FoodPaymentController extends Controller
{
    public function __construct()
    {
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = config('midtrans.is_sanitized');
        Config::$is3ds = config('midtrans.is_3ds');
    }

    public function createPayment(Request $request)
    {
        try {
            $validated = $request->validate([
                'amount' => 'required|numeric|min:1000',
                'customer_name' => 'required|string|max:255',
                'customer_email' => 'required|email|max:255',
                'items' => 'required|array|min:1',
                'items.*.food_id' => 'required|integer',
                'items.*.quantity' => 'required|integer|min:1',
                'items.*.price' => 'required|numeric|min:0'
            ]);
            
            // Convert numeric values to integers
            $validated['amount'] = (int) $validated['amount'];
            foreach ($validated['items'] as &$item) {
                $item['price'] = (int) $item['price'];
            }

            $orderId = 'FOOD-' . time() . '-' . Str::random(8);
            
            // Get food details for item_details
            $itemDetails = [];
            foreach ($validated['items'] as $item) {
                $food = Food::find($item['food_id']);
                $itemDetails[] = [
                    'id' => 'food-' . $item['food_id'],
                    'price' => $item['price'],
                    'quantity' => $item['quantity'],
                    'name' => $food ? $food->name : 'Food Item'
                ];
            }
            
            $params = [
                'transaction_details' => [
                    'order_id' => $orderId,
                    'gross_amount' => $validated['amount']
                ],
                'customer_details' => [
                    'first_name' => $validated['customer_name'],
                    'email' => $validated['customer_email']
                ],
                'item_details' => $itemDetails,
                'callbacks' => [
                    'finish' => config('app.url') . '/booking-success',
                    'unfinish' => config('app.url') . '/food-payment',
                    'error' => config('app.url') . '/food-payment'
                ]
            ];
            
            $snapToken = Snap::createTransaction($params)->token;
            
            FoodTransaction::create([
                'order_id' => $orderId,
                'customer_name' => $validated['customer_name'],
                'customer_email' => $validated['customer_email'],
                'amount' => $validated['amount'],
                'total_amount' => $validated['amount'],
                'status' => 'pending',
                'items' => $validated['items'],
                'snap_token' => $snapToken
            ]);
            
            Log::info('Food payment token created', [
                'order_id' => $orderId,
                'amount' => $validated['amount']
            ]);
            
            return response()->json([
                'success' => true,
                'snap_token' => $snapToken,
                'order_id' => $orderId
            ]);
            
        } catch (\Exception $e) {
            Log::error('Food payment error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Payment processing failed: ' . $e->getMessage()
            ], 500);
        }
    }
}