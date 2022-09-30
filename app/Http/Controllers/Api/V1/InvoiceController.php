<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        if ($request->order_id) {
            $invoices = Invoice::with(['user', 'order'])->where('user_id', $request->user()->id)->where('order_id', $request->order_id)->get();
        } else {
            $invoices = Invoice::with(['user', 'order'])->where('user_id', $request->user()->id)->get();
        }

        return response()->json([
            'success' => true,
            'message' => 'data invoices',
            'data' => $invoices
        ]);
    }
}
