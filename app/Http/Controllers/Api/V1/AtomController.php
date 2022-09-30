<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Atom;
use Illuminate\Http\Request;

class AtomController extends Controller
{
    public function showByOrdinal(Request $request, $id, $ordinal)
    {
        $atom = Atom::where('id', $id)->where('ordinal', $ordinal)->with('molecule')->first();

        return response()->json([
            'success' => true,
            'message' => 'data atom',
            'data' => $atom
        ]);
    }
}
