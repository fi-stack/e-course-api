<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Mail\OrderMail;
use App\Models\Order;
use App\Models\Service;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $orders = Order::where('user_id', $request->user()->id)->get();

        foreach ($orders as $order) {
            $order->image = url(Storage::url('uploads/orders/' . $order->image));
            $order->service = json_decode($order->service);
            $discount = ($order->service->discount / 100) * $order->service->price;
            $order->service->total = $order->service->price - $discount;
            $order->service->price = rupiah($order->service->price);
            $order->service->discount = $order->service->discount . '%';
            $order->service->total = rupiah($order->service->total);
            $order->left_at = date('H:i:s', strtotime($order->expired_at) - strtotime('now'));
            $order->expired_at = date('Y-m-d H:i:s', $order->expired_at);
        }

        return response()->json([
            'success' => true,
            'message' => 'data orders',
            'data' => $orders
        ]);
    }

    public function show(Request $request, $id)
    {
        $order = Order::where('id', $id)->where('user_id', $request->user()->id)->first();

        $order->image = url(Storage::url('uploads/orders/' . $order->image));
        $order->service = json_decode($order->service);
        $discount = ($order->service->discount / 100) * $order->service->price;
        $order->service->total = $order->service->price - $discount;
        $order->service->price = rupiah($order->service->price);
        $order->service->discount = $order->service->discount . '%';
        $order->service->total = rupiah($order->service->total);
        $order->left_at = date('H:i:s', strtotime($order->expired_at) - strtotime('now'));
        $order->expired_at = date('Y-m-d H:i:s', $order->expired_at);

        return response()->json([
            'success' => true,
            'message' => 'data order',
            'data' => $order
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'service_id' => 'required',
            'payment_type' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'validation failed',
                'data' => $validator->errors()
            ], 400);
        }

        $user = User::where('id', $request->user_id)->first();
        $service = Service::where('id', $request->service_id)->first();
        $discount = ($service->discount / 100) * $service->price;
        $service->total = $service->price - $discount;

        $expiredAt = strtotime('now') + 86400;

        $order = Order::create([
            'service' => $service,
            'cart' => $request->cart,
            'user_id' => $request->user()->id,
            'payment_type' => $request->payment_type,
            'expired_at' => $expiredAt,
            'status' => 'waiting'
        ]);

        $service->price = rupiah($service->price);
        $service->discount = $service->discount . '%';
        $service->total = rupiah($service->total);
        $service->expired_at = date('Y-m-d H:i:s', $order->expired_at);

        $data = [
            'service' => $service,
            'user' => $user,
            'payment_type' => $request->payment_type,
            'expired_at' => date('Y-m-d H:i:s', $expiredAt)
        ];

        Mail::to($user->email)->send(new OrderMail($data));

        return response()->json([
            'success' => true,
            'message' => 'order berhasil',
            'data' => $order
        ]);
    }

    public function upload(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'image' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'validation failed',
                'data' => $validator->errors()
            ], 400);
        }

        $image_64 = $request->image;
        $extension = explode('/', explode(':', substr($image_64, 0, strpos($image_64, ';')))[1])[1];
        $replace = substr($image_64, 0, strpos($image_64, ',') + 1);
        $image = str_replace($replace, '', $image_64);
        $image = str_replace(' ', '+', $image);
        $imageName = Str::random(10) . '.' . $extension;
        $imagePath = '/uploads/orders/' . $imageName;
        Storage::disk('public')->put($imagePath, base64_decode($image));

        $order = Order::where('id', $id)->first();

        $order->update([
            'image' => $imageName,
            'status' => 'wait'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'upload pembayaran berhasil',
            'data' => $order
        ]);
    }
}
