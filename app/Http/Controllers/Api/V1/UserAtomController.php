<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Atom;
use App\Models\UserAtom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserAtomController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'atom_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'validation failed',
                'data' => $validator->errors()
            ], 400);
        }

        $findUserAtom = UserAtom::where('user_id', $request->user()->id)->where('atom_id', $request->atom_id)->first();

        if ($findUserAtom) {
            return response()->json([
                'success' => false,
                'message' => 'bagian ini sudah ditandai selesai'
            ]);
        }

        $userAtom = UserAtom::create([
            'user_id' => $request->user()->id,
            'atom_id' => $request->atom_id
        ]);

        $atom = Atom::where('id', $userAtom->atom_id)->first();
        $next = Atom::where('molecule_id', $atom->molecule_id)->where('ordinal', $atom->ordinal + 1)->first();
        if (!$next) {
            $next = Atom::where('molecule_id', $atom->molecule_id + 1)->where('ordinal', 1)->first();
        }

        if (!$next) {
            $result = Atom::where('molecule_id', $atom->molecule_id)->where('ordinal', $atom->ordinal)->first();

            return response()->json([
                'success' => true,
                'message' => 'selamat, anda telah menyelesaikan kursus ini',
                'data' => $result
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'user atom berhasil ditambahkan',
            'data' => $next
        ]);
    }
}
