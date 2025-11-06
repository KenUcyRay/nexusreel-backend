<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Midtrans\Config;
use Midtrans\CoreApi;
use Illuminate\Support\Facades\Log;

class MidtransTestController extends Controller
{
    public function testConnection()
    {
        try {
            Config::$serverKey = config('midtrans.server_key');
            Config::$isProduction = config('midtrans.is_production');
            
            // Test ping to Midtrans API
            $testOrderId = 'TEST-' . time();
            
            $params = [
                'payment_type' => 'bank_transfer',
                'transaction_details' => [
                    'order_id' => $testOrderId,
                    'gross_amount' => 10000
                ],
                'bank_transfer' => [
                    'bank' => 'bca'
                ]
            ];
            
            $response = CoreApi::charge($params);
            
            return response()->json([
                'success' => true,
                'message' => 'Midtrans connection successful',
                'config' => [
                    'server_key_set' => !empty(config('midtrans.server_key')),
                    'client_key_set' => !empty(config('midtrans.client_key')),
                    'is_production' => config('midtrans.is_production'),
                    'is_sanitized' => config('midtrans.is_sanitized'),
                    'is_3ds' => config('midtrans.is_3ds')
                ],
                'test_response' => $response
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Midtrans connection failed',
                'error' => $e->getMessage(),
                'config' => [
                    'server_key_set' => !empty(config('midtrans.server_key')),
                    'client_key_set' => !empty(config('midtrans.client_key')),
                    'is_production' => config('midtrans.is_production')
                ]
            ], 500);
        }
    }
}