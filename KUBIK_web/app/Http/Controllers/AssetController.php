<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Models\Asset;
use App\Models\AssetMaster;

class AssetController extends Controller
{
    /**
     * Tampilkan semua aset dengan informasi master dan kondisi
     */
    public function index()
    {
        $assets = Asset::with(['master:id_master,name'])
            ->select('id_asset', 'id_master', 'status', 'asset_condition', 'created_at')
            ->get();

        return response()->json([
            'message' => 'Daftar seluruh aset',
            'data' => $assets
        ]);
    }

    /**
     * Tampilkan satu aset berdasarkan ID
     */
    public function show($id)
    {
        $asset = Asset::with(['master:id_master,name'])
            ->where('id_asset', $id)
            ->first();

        if (!$asset) {
            return response()->json(['message' => 'Aset tidak ditemukan'], 404);
        }

        return response()->json(['data' => $asset]);
    }

    /**
     * Perbarui kondisi aset (misal: Good, Damaged, Lost)
     * Smart Logic: update stok master otomatis
     */
    public function updateCondition(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'asset_condition' => 'required|in:Good,Damaged,Lost'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $asset = Asset::find($id);

        if (!$asset) {
            return response()->json(['message' => 'Aset tidak ditemukan'], 404);
        }

        // Update kondisi aset
        $asset->update(['asset_condition' => $request->asset_condition]);

        // Smart Logic: update stok tersedia di asset_master
        $this->syncStockAvailable($asset->id_master);

        return response()->json([
            'message' => 'Kondisi aset berhasil diperbarui dan stok disinkronkan',
            'data' => $asset
        ]);
    }

    /**
     * Tandai aset sedang dipinjam (status = borrowed)
     */
    public function markBorrowed($id)
    {
        $asset = Asset::find($id);

        if (!$asset) {
            return response()->json(['message' => 'Aset tidak ditemukan'], 404);
        }

        if ($asset->status === 'borrowed') {
            return response()->json(['message' => 'Aset sudah dipinjam'], 409);
        }

        $asset->update(['status' => 'Borrowed']);

        // Smart Logic: sinkron stok
        $this->syncStockAvailable($asset->id_master);

        return response()->json(['message' => 'Aset berhasil ditandai sebagai dipinjam']);
    }

    /**
     * Tandai aset sudah dikembalikan (status = available)
     */
    public function markReturned($id)
    {
        $asset = Asset::find($id);

        if (!$asset) {
            return response()->json(['message' => 'Aset tidak ditemukan'], 404);
        }

        $asset->update(['status' => 'available']);

        // Smart Logic: sinkron stok tersedia
        $this->syncStockAvailable($asset->id_master);

        return response()->json(['message' => 'Aset berhasil ditandai sebagai tersedia kembali']);
    }

    /**
     * Smart Logic: sinkronisasi stok tersedia
     */
    private function syncStockAvailable($id_master)
    {
        $availableCount = Asset::where('id_master', $id_master)
            ->where('status', 'Available')
            ->where('asset_condition', 'Good')
            ->count();

        AssetMaster::where('id_master', $id_master)
            ->update(['stock_available' => $availableCount]);
    }
}
