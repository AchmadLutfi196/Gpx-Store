<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use Midtrans\Snap;
use Midtrans\Config;

class MidtransController extends Controller
{
    public function initiatePayment(Request $request)
    {
        $transaction = Transaction::find($request->transaction_id);
        
        if (!$transaction) {
            return response()->json(['message' => 'Transaksi tidak ditemukan'], 404);
        }

        Config::$serverKey = config('services.midtrans.server_key');
        Config::$isProduction = config('services.midtrans.is_production');
        Config::$isSanitized = true;
        Config::$is3ds = true;

        $params = [
            'transaction_details' => [
                'order_id' => $transaction->order_id,
                'gross_amount' => $transaction->total_amount,
            ],
            'customer_details' => [
                'email' => $transaction->user->email,
                'first_name' => $transaction->user->name,
            ],
        ];

        try {
            $snapToken = Snap::getSnapToken($params);
            return response()->json(['snapToken' => $snapToken]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function handleNotification(Request $request)
    {
        $serverKey = config('services.midtrans.server_key');
        $hashed = hash("sha512", $request->order_id . $request->status_code . $request->gross_amount . $serverKey);

        if ($hashed != $request->signature_key) {
            return response()->json(['message' => 'Invalid signature'], 403);
        }

        $transaction = Transaction::where('order_id', $request->order_id)->first();
        if (!$transaction) {
            return response()->json(['message' => 'Transaksi tidak ditemukan'], 404);
        }

        if ($request->transaction_status == 'settlement') {
            $transaction->update(['status' => 'settlement']);
        } elseif ($request->transaction_status == 'expire') {
            $transaction->update(['status' => 'expire']);
        } elseif ($request->transaction_status == 'cancel') {
            $transaction->update(['status' => 'cancel']);
        }

        return response()->json(['message' => 'Transaksi diperbarui'], 200);
    }
}
