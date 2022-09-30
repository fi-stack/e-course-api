<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Mail\OrderMail;
use App\Models\Order;
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
            $order->service = json_decode($order->service);
            $order->image = url(Storage::url('uploads/orders/' . $order->image));
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

        $order->service = json_decode($order->service);
        $order->image = url(Storage::url('uploads/orders/' . $order->image));

        return response()->json([
            'success' => true,
            'message' => 'data order',
            'data' => $order
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'service' => 'required',
            'user_id' => 'required',
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

        $data = [
            'service' => json_decode($request->service),
            'user' => $user,
            'payment_type' => $request->payment_type,
        ];

        Mail::to($user->email)->send(new OrderMail($data));

        if (Mail::failures()) {
            return response()->json([
                'success' => false,
                'message' => 'Sorry! Please try again latter'
            ], 400);
        } else {
            $order = Order::create([
                'service' => $request->service,
                'cart' => $request->cart,
                'user_id' => $request->user()->id,
                'payment_type' => $request->payment_type,
                'status' => 'waiting'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'order berhasil',
                'data' => $order
            ]);
        }
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
