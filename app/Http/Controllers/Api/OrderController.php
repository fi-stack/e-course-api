<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\Subscriber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with('user')->get();

        foreach ($orders as $order) {
            $order->service = json_decode($order->service);
            $order->image = url(Storage::url('uploads/orders/' . $order->image));
        }

        return response()->json([
            'success' => true,
            'message' => 'data order',
            'data' => $orders
        ]);
    }

    public function approve($id)
    {
        $order = Order::where('id', $id)->first();

        $order->update([
            'status' => 'success'
        ]);

        Invoice::create([
            'invoice' => date('Y-m-d') . '/' . rand(0, 99999),
            'at' => date('Y-m-d H:i:s'),
            'user_id' => $order->user_id,
            'order_id' => $order->id
        ]);

        $order->service = json_decode($order->service);

        Subscriber::create([
            'user_id' => $order->user_id,
            'active_at' => strtotime(date('Y-m-d H:i:s')),
            'expired_at' => strtotime(date('Y-m-d H:i:s')) + $order->service->duration,
            'status' => 'active'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'invoice berhasil dibuat'
        ]);
    }
}
