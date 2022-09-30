<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ServiceController extends Controller
{
    public function index(Request $request)
    {
        $services = Service::get();

        return response()->json([
            'success' => true,
            'message' => 'data services',
            'data' => $services
        ]);
    }

    public function show(Request $request, $id)
    {
        $service = Service::where('id', $id)->first();

        return response()->json([
            'success' => true,
            'message' => 'data service',
            'data' => $service
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'duration' => 'required',
            'price' => 'required',
            'discount' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'validation failed',
                'data' => $validator->errors()
            ], 400);
        }

        $service = Service::create([
            'name' => $request->name,
            'duration' => $request->duration * 86400,
            'price' => $request->price,
            'discount' => $request->discount
        ]);

        return response()->json([
            'success' => true,
            'message' => 'data service berhasil dibuat',
            'data' => $service
        ]);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'duration' => 'required',
            'price' => 'required',
            'discount' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'validation failed',
                'data' => $validator->errors()
            ], 400);
        }

        $service = Service::where('id', $id)->first();

        $service->update([
            'name' => $request->name,
            'duration' => $request->duration * 86400,
            'price' => $request->price,
            'discount' => $request->discount
        ]);

        return response()->json([
            'success' => true,
            'message' => 'service berhasil diubah',
            'data' => $service
        ]);
    }

    public function destroy(Request $request, $id)
    {
        $service = Service::where('id', $id)->first();

        $service->delete();

        return response()->json([
            'success' => true,
            'message' => 'service berhasil dihapus',
            'data' => $service
        ]);
    }
}
