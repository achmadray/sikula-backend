<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Satuan;
use Illuminate\Http\Request;

class SatuanController extends Controller
{
    public function index()
    {
        return response()->json(Satuan::all());
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_satuan' => 'required|string|max:50',
        ]);

        $satuan = Satuan::create($request->all());

        return response()->json([
            'message' => 'Satuan berhasil ditambahkan',
            'data' => $satuan
        ], 201);
    }

    public function show($id)
    {
        $satuan = Satuan::find($id);

        if (!$satuan) {
            return response()->json(['message' => 'Satuan tidak ditemukan'], 404);
        }

        return response()->json($satuan);
    }

    public function update(Request $request, $id)
    {
        $satuan = Satuan::find($id);

        if (!$satuan) {
            return response()->json(['message' => 'Satuan tidak ditemukan'], 404);
        }

        $request->validate([
            'nama_satuan' => 'required|string|max:50',
        ]);

        $satuan->update($request->all());

        return response()->json([
            'message' => 'Satuan berhasil diperbarui',
            'data' => $satuan
        ]);
    }

    public function destroy($id)
    {
        $satuan = Satuan::find($id);

        if (!$satuan) {
            return response()->json(['message' => 'Satuan tidak ditemukan'], 404);
        }

        $satuan->delete();

        return response()->json(['message' => 'Satuan berhasil dihapus']);
    }
}
