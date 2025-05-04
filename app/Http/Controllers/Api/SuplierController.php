<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Suplier;
use Illuminate\Http\Request;

class SuplierController extends Controller
{
    public function index()
    {
        $supliers = Suplier::all();
        return response()->json($supliers);
    }

    public function simpan(Request $request)
    {
        $request->validate([
            'nama_suplier' => 'required|string|max:255',
            'no_telpon' => 'required|string|max:20',
            'alamat' => 'required|string|max:255',
        ]);

        $suplier = Suplier::tambah($request->all());
        return response()->json($suplier, 201);
    }

    public function tampil($id)
    {
        $suplier = Suplier::find($id);

        if (!$suplier) {
            return response()->json(['message' => 'Suplier not found'], 404);
        }

        return response()->json($suplier);
    }

    public function update(Request $request, $id)
    {
        $suplier = Suplier::find($id);

        if (!$suplier) {
            return response()->json(['message' => 'Suplier not found'], 404);
        }

        $suplier->update($request->all());
        return response()->json($suplier);
    }

    public function delete($id)
    {
        $suplier = Suplier::find($id);

        if (!$suplier) {
            return response()->json(['message' => 'Suplier not found'], 404);
        }

        $suplier->delete();
        return response()->json(['message' => 'Suplier deleted successfully']);
    }
}
