<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Atom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AtomController extends Controller
{
    public function index(Request $request)
    {
        $atoms = Atom::with('molecule')->get();

        return response()->json([
            'success' => true,
            'message' => 'data atom',
            'data' => $atoms
        ]);
    }

    public function show(Request $request, $id)
    {
        $atom = Atom::where('id', $id)->with('molecule')->first();

        return response()->json([
            'success' => true,
            'message' => 'data atom',
            'data' => $atom
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ordinal' => 'required',
            'title' => 'required',
            'molecule_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'validation failed',
                'data' => $validator->errors()
            ], 400);
        }

        $atom = Atom::create([
            'ordinal' => $request->ordinal,
            'title' => $request->title,
            'molecule_id' => $request->molecule_id
        ]);

        return response()->json([
            'success' => true,
            'message' => 'data atom berhasil dibuat',
            'data' => $atom
        ]);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'ordinal' => 'required',
            'title' => 'required',
            'molecule_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'validation failed',
                'data' => $validator->errors()
            ], 400);
        }

        $atom = Atom::where('id', $id)->first();

        $atom->update([
            'ordinal' => $request->ordinal,
            'title' => $request->title,
            'molecule_id' => $request->molecule_id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'atom berhasil diubah',
            'data' => $atom
        ]);
    }

    public function destroy(Request $request, $id)
    {
        $atom = Atom::where('id', $id)->first();

        $atom->delete();

        return response()->json([
            'success' => true,
            'message' => 'atom berhasil dihapus',
            'data' => $atom
        ]);
    }
}
