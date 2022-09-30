<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Molecule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MoleculeController extends Controller
{
    public function index(Request $request)
    {
        $molecules = Molecule::with('course')->get();

        return response()->json([
            'success' => true,
            'message' => 'data molekul',
            'data' => $molecules
        ]);
    }

    public function show(Request $request, $id)
    {
        $molecule = Molecule::where('id', $id)->with('course')->first();

        return response()->json([
            'success' => true,
            'message' => 'data molekul',
            'data' => $molecule
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ordinal' => 'required',
            'name' => 'required',
            'course_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'validation failed',
                'data' => $validator->errors()
            ], 400);
        }

        $molecule = Molecule::create([
            'ordinal' => $request->ordinal,
            'name' => $request->name,
            'course_id' => $request->course_id
        ]);

        return response()->json([
            'success' => true,
            'message' => 'data molekul berhasil dibuat',
            'data' => $molecule
        ]);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'ordinal' => 'required',
            'name' => 'required',
            'course_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'validation failed',
                'data' => $validator->errors()
            ], 400);
        }

        $molecule = Molecule::where('id', $id)->first();

        $molecule->update([
            'ordinal' => $request->ordinal,
            'name' => $request->name,
            'course_id' => $request->course_id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'molekul berhasil diubah',
            'data' => $molecule
        ]);
    }

    public function destroy(Request $request, $id)
    {
        $molecule = Molecule::where('id', $id)->first();

        $molecule->delete();

        return response()->json([
            'success' => true,
            'message' => 'molekul berhasil dihapus',
            'data' => $molecule
        ]);
    }
}
