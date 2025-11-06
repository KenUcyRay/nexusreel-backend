<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Midtrans\Config;
use Midtrans\Snap;
use Midtrans\Notification;
use App\Models\MovieTransaction;
use App\Models\FoodTransaction;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class MidtransController extends Controller
{
    public function __construct()
    {
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = config('midtrans.is_sanitized');
        Config::$is3ds = config('midtrans.is_3ds');
    }

    public function createTransaction(Request $request)
    {
        try {
            $validated = $request->validate([
                'amount' => 'required|integer|min:1000',
                'customer_name' => 'required|string|max:255',
                'customer_email' => 'required|email|max:255',
                'seats' => 'required|array|min:1',
                'ticket_count' => 'required|integer|min:1',
                'schedule_id' => 'required|integer'
            ]);

            $orderId = 'ORDER-' . time() . '-' . Str::random(8);
            
            $params = [
                'transaction_details' => [
                    'order_id' => $orderId,
                    'gross_amount' => $validated['amount'],
                ],
                'customer_details' => [
                    'first_name' => $validated['customer_name'],
                    'email' => $validated['customer_email'],
                ],
                'callbacks' => [
                    'finish' => config('app.url') . '/booking-success',
                    'unfinish' => config('app.url') . '/payment',
                    'error' => config('app.url') . '/payment'
                ]
            ];

            $snapToken = Snap::createTransaction($params)->token;
            
            MovieTransaction::create([
                'order_id' => $orderId,
                'customer_name' => $validated['customer_name'],
                'customer_email' => $validated['customer_email'],
                'amount' => $validated['amount'],
                'status' => 'pending',
                'seats' => $validated['seats'],
                'ticket_count' => $validated['ticket_count'],
                'schedule_id' => $validated['schedule_id'],
                'snap_token' => $snapToken
            ]);

            Log::info('Snap token created successfully', [
                'order_id' => $orderId,
                'amount' => $validated['amount']
            ]);
            
            return response()->json([
                'success' => true,
                'snap_token' => $snapToken,
                'order_id' => $orderId
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Payment processing failed: ' . $e->getMessage()
            ], 500);
        }
    }

    public function createKasirTransaction(Request $request)
    {
        try {
            $validated = $request->validate([
                'amount' => 'required|integer|min:1000',
                'customer_name' => 'required|string|max:255',
                'seats' => 'required|array|min:1',
                'ticket_count' => 'required|integer|min:1',
                'schedule_id' => 'required|integer'
            ]);

            $orderId = 'KASIR-' . time() . '-' . Str::random(8);
            
            $params = [
                'transaction_details' => [
                    'order_id' => $orderId,
                    'gross_amount' => $validated['amount'],
                ],
                'customer_details' => [
                    'first_name' => $validated['customer_name'],
                    'email' => 'kasir@nexuscinema.com'
                ],
                'callbacks' => [
                    'finish' => config('app.url') . '/kasir/success',
                    'unfinish' => config('app.url') . '/kasir/payment',
                    'error' => config('app.url') . '/kasir/payment'
                ]
            ];

            $snapToken = Snap::createTransaction($params)->token;
            
            MovieTransaction::create([
                'order_id' => $orderId,
                'customer_name' => $validated['customer_name'],
                'customer_email' => 'kasir@nexuscinema.com',
                'amount' => $validated['amount'],
                'status' => 'pending',
                'seats' => $validated['seats'],
                'ticket_count' => $validated['ticket_count'],
                'schedule_id' => $validated['schedule_id'],
                'snap_token' => $snapToken
            ]);

            Log::info('Kasir payment token created', [
                'order_id' => $orderId,
                'customer_name' => $validated['customer_name'],
                'amount' => $validated['amount']
            ]);
            
            return response()->json([
                'success' => true,
                'snap_token' => $snapToken,
                'order_id' => $orderId
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Kasir payment processing failed: ' . $e->getMessage()
            ], 500);
        }
    }

    public function callback(Request $request)
    {
        try {
            // Handle both real webhook and simulation
            if ($request->has('transaction_status')) {
                // Simulation or direct request
                $status = $request->input('transaction_status');
                $orderId = $request->input('order_id');
                $fraudStatus = $request->input('fraud_status');
            } else {
                // Real Midtrans webhook
                $notification = new Notification();
                $status = $notification->transaction_status;
                $orderId = $notification->order_id;
                $fraudStatus = $notification->fraud_status ?? null;
            }
            
            Log::info('Midtrans notification received', [
                'order_id' => $orderId,
                'transaction_status' => $status,
                'fraud_status' => $fraudStatus
            ]);

            // Check transaction type by order ID prefix
            if (str_starts_with($orderId, 'FOOD-')) {
                $transaction = FoodTransaction::where('order_id', '=', $orderId)->first();
            } elseif (str_starts_with($orderId, 'KASIR-') || str_starts_with($orderId, 'ORDER-')) {
                $transaction = MovieTransaction::where('order_id', '=', $orderId)->first();
            } else {
                $transaction = MovieTransaction::where('order_id', '=', $orderId)->first();
            }
            
            if (!$transaction) {
                Log::warning('Transaction not found', ['order_id' => $orderId]);
                return response()->json(['status' => 'transaction not found'], 404);
            }

            // Handle all possible transaction statuses
            switch ($status) {
                case 'capture':
                    if ($fraudStatus == 'accept') {
                        $transaction->update(['status' => 'success']);
                        Log::info('Transaction captured and accepted', ['order_id' => $orderId]);
                    } else {
                        $transaction->update(['status' => 'pending']);
                        Log::info('Transaction captured but pending fraud review', ['order_id' => $orderId]);
                    }
                    break;
                    
                case 'settlement':
                    $transaction->update(['status' => 'success']);
                    Log::info('Transaction settled successfully', ['order_id' => $orderId]);
                    break;
                    
                case 'pending':
                    $transaction->update(['status' => 'pending']);
                    Log::info('Transaction pending', ['order_id' => $orderId]);
                    break;
                    
                case 'deny':
                case 'cancel':
                case 'expire':
                case 'failure':
                    $transaction->update(['status' => 'failed']);
                    Log::info('Transaction failed/cancelled/expired', [
                        'order_id' => $orderId,
                        'status' => $status
                    ]);
                    break;
                    
                default:
                    Log::warning('Unknown transaction status', [
                        'order_id' => $orderId,
                        'status' => $status
                    ]);
            }

            return response()->json(['status' => 'ok']);
            
        } catch (\Exception $e) {
            Log::error('Midtrans callback error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['status' => 'error'], 500);
        }
    }
}