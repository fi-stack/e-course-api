<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index(Request $request)
    {
        $services = Service::get();

        foreach ($services as $service) {
            $priceDiscount = ($service->discount / 100) * $service->price;
            $service->total = rupiah($service->price - $priceDiscount);
            $service->price = rupiah($service->price);
        }

        return response()->json([
            'success' => true,
            'message' => 'data services',
            'data' => $services
        ]);
    }

    public function show($id)
    {
        $service = Service::where('id', $id)->first();

        $priceDiscount = ($service->discount / 100) * $service->price;
        $service->total = rupiah($service->price - $priceDiscount);
        $service->price = rupiah($service->price);

        return response()->json([
            'success' => true,
            'message' => 'data service',
            'data' => $service
        ]);
    }
}
