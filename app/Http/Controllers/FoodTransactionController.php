<?php

namespace App\Http\Controllers;

use App\Models\Food;
use App\Models\FoodTransaction;
use App\Models\FoodTransactionItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class FoodTransactionController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'items' => 'required|array|min:1',
            'items.*.food_id' => 'required|exists:foods,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            $totalAmount = 0;
            $validatedItems = [];

            // Validate items and calculate total
            foreach ($request->items as $item) {
                $food = Food::where('id', '=', $item['food_id'])
                           ->where('is_active', '=', true)
                           ->first();

                if (!$food) {
                    return response()->json([
                        'success' => false,
                        'message' => "Food item with ID {$item['food_id']} not found or inactive"
                    ], 404);
                }

                $subtotal = $item['quantity'] * $item['price'];
                $totalAmount += $subtotal;

                $validatedItems[] = [
                    'food_id' => $food->id,
                    'food_name' => $food->name,
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'subtotal' => $subtotal,
                ];
            }

            // Create transaction
            $transaction = FoodTransaction::create([
                'customer_name' => $request->customer_name,
                'customer_email' => $request->customer_email,
                'total_amount' => $totalAmount,
                'status' => 'completed',
            ]);

            // Create transaction items
            foreach ($validatedItems as $item) {
                $transaction->items()->create($item);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'data' => [
                    'transaction_id' => $transaction->id,
                    'total_amount' => $totalAmount,
                    'status' => $transaction->status,
                ],
                'message' => 'Food order created successfully'
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to create food order',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}