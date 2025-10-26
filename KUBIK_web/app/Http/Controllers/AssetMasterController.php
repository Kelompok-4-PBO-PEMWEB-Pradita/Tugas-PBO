<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Models\AssetMaster;
use App\Models\Asset;
use App\Models\Type;
use App\Models\Category;

class AssetMasterController extends Controller
{
    /**
     * Show all asset masters with category, type, and stock info
     */
    public function index()
    {
        $assets = AssetMaster::with(['category:id_category,name', 'type:id_type,name'])
            ->select('id_master', 'name', 'id_category', 'id_type', 'stock_total', 'stock_available')
            ->get();

        return response()->json([
            'message' => 'List of all asset masters',
            'data' => $assets
        ]);
    }

    /**
     * Show single asset master
     */
    public function show($id)
    {
        $asset = AssetMaster::with(['category:id_category,name', 'type:id_type,name', 'assets:id_asset,id_master,asset_condition'])
            ->find($id);

        if (!$asset) {
            return response()->json(['message' => 'Asset master not found'], 404);
        }

        return response()->json(['data' => $asset]);
    }

    /**
     * Create new asset master + generate assets automatically
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_category' => 'required|exists:categories,id_category',
            'id_type' => 'required|exists:types,id_type',
            'name' => 'required|string|max:255',
            'stock_total' => 'required|integer|min:1',
            'description' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Smart Logic: generate ID prefix otomatis AM-000001
        $lastId = AssetMaster::orderBy('created_at', 'desc')->first();
        $nextNumber = $lastId ? intval(substr($lastId->id_master, 3)) + 1 : 1;
        $newId = 'AM-' . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);

        $assetMaster = AssetMaster::create([
            'id_master' => $newId,
            'id_category' => $request->id_category,
            'id_type' => $request->id_type,
            'name' => $request->name,
            'description' => $request->description,
            'stock_total' => $request->stock_total,
            'stock_available' => $request->stock_total,
        ]);
        
        return response()->json([
            'message' => 'Asset master created successfully with all assets generated.',
            'data' => $assetMaster
        ], 201);
    }

    /**
     * Update asset master data
     */
    public function update(Request $request, $id)
    {
        $assetMaster = AssetMaster::find($id);

        if (!$assetMaster) {
            return response()->json(['message' => 'Asset master not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'stock_total' => 'required|integer|min:1'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $assetMaster->update([
            'name' => $request->name,
            'description' => $request->description,
            'stock_total' => $request->stock_total,
        ]);

        // Smart Logic: sinkron stok_available dengan jumlah aset aktual
        $currentAssetCount = Asset::where('id_master', $id)->count();
        if ($currentAssetCount < $request->stock_total) {
            for ($i = $currentAssetCount + 1; $i <= $request->stock_total; $i++) {
                Asset::create([
                    'id_master' => $assetMaster->id_master,
                    'asset_condition' => 'good'
                ]);
            }
        }

        $assetMaster->update([
            'stock_available' => Asset::where('id_master', $id)
                                      ->where('asset_condition', 'good')->count()
        ]);

        return response()->json([
            'message' => 'Asset master updated successfully and stock synchronized.',
            'data' => $assetMaster
        ]);
    }

    /**
     * Delete asset master (and its assets)
     */
    public function destroy($id)
    {
        $assetMaster = AssetMaster::find($id);

        if (!$assetMaster) {
            return response()->json(['message' => 'Asset master not found'], 404);
        }

        // Smart Logic: cascade delete all related assets
        Asset::where('id_master', $id)->delete();
        $assetMaster->delete();

        return response()->json(['message' => 'Asset master and all related assets deleted successfully.']);
    }
}
