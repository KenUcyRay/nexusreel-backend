<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MovieTransaction;
use App\Models\FoodTransaction;
use Illuminate\Support\Facades\Log;

class MidtransSimulatorController extends Controller
{
    public function simulateWebhook(Request $request)
    {
        $orderId = $request->input('order_id');
        $status = $request->input('status', 'settlement');
        
        if (!$orderId) {
            return response()->json(['error' => 'order_id required'], 400);
        }
        
        // Simulate webhook payload
        $webhookData = [
            'transaction_status' => $status,
            'order_id' => $orderId,
            'fraud_status' => 'accept',
            'transaction_id' => 'test-' . time(),
            'gross_amount' => '50000.00'
        ];
        
        Log::info('Simulating Midtrans webhook', $webhookData);
        
        // Create proper request for webhook
        $webhookRequest = Request::create('/midtrans/notification', 'POST', $webhookData);
        $controller = new \App\Http\Controllers\MidtransController();
        $response = $controller->callback($webhookRequest);
        
        return response()->json([
            'message' => 'Webhook simulation sent',
            'data' => $webhookData,
            'response' => $response->getData()
        ]);
    }
    
    public function checkTransactionStatus($orderId)
    {
        if (str_starts_with($orderId, 'FOOD-')) {
            $transaction = FoodTransaction::where('order_id', $orderId)->first();
        } else {
            $transaction = MovieTransaction::where('order_id', $orderId)->first();
        }
        
        if (!$transaction) {
            return response()->json(['error' => 'Transaction not found'], 404);
        }
        
        return response()->json([
            'order_id' => $transaction->order_id,
            'status' => $transaction->status,
            'amount' => $transaction->amount ?? $transaction->total_amount,
            'created_at' => $transaction->created_at,
            'updated_at' => $transaction->updated_at
        ]);
    }
}