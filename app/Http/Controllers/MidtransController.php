<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Midtrans\Config;
use Midtrans\Snap;
use Midtrans\Notification;
use App\Models\MovieTransaction;
use Illuminate\Support\Str;

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
            ];

            $snapToken = Snap::getSnapToken($params);
            
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

            return response()->json(['snap_token' => $snapToken]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Payment processing failed: ' . $e->getMessage()
            ], 500);
        }
    }

    public function callback(Request $request)
    {
        $notification = new Notification();
        $status = $notification->transaction_status;
        $orderId = $notification->order_id;

        $transaction = MovieTransaction::where('order_id', '=', $orderId)->first();
        
        if ($transaction) {
            if ($status == 'capture' || $status == 'settlement') {
                $transaction->update(['status' => 'success']);
            } elseif ($status == 'pending') {
                $transaction->update(['status' => 'pending']);
            } elseif ($status == 'deny' || $status == 'expire' || $status == 'cancel') {
                $transaction->update(['status' => 'failed']);
            }
        }

        return response()->json(['status' => 'ok']);
    }
}