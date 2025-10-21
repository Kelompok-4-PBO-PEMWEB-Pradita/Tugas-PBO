<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\AssetMaster;
use App\Models\Asset;
use App\Models\Category;
use App\Models\Type;

class AssetController extends Controller
{
    // List asset masters with pagination + include assets
    public function index(Request $request)
    {
        $q = $request->query('q');
        $perPage = (int) $request->query('per_page', 20);

        $query = AssetMaster::with('assets', 'category', 'type')->orderByDesc('created_at');

        if ($q) {
            $query->where('name', 'like', "%{$q}%");
        }

        $data = $query->paginate($perPage);

        return response()->json($data);
    }

    // Create asset master + auto-generate assets units
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'id_category' => 'required|string|exists:categories,id_category',
            'id_type' => 'required|string|exists:types,id_type',
            'stock_total' => 'required|integer|min:1'
        ]);

        DB::beginTransaction();
        try {
            // Create master (DB trigger will set id_master)
            $master = AssetMaster::create([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'id_category' => $validated['id_category'],
                'id_type' => $validated['id_type'],
                'stock_total' => $validated['stock_total'],
                'stock_available' => $validated['stock_total']
            ]);

            // Generate unit assets equal to stock_total
            for ($i = 0; $i < $validated['stock_total']; $i++) {
                Asset::create([
                    'id_master' => $master->id_master,
                    'status' => 'available',
                    'condition' => 'good'
                ]);
            }

            DB::commit();
            return response()->json(['message' => 'Asset master created', 'data' => $master], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to create asset master', 'message' => $e->getMessage()], 500);
        }
    }

    // Show asset master + units
    public function show($id_master)
    {
        $master = AssetMaster::with('assets','category','type')->findOrFail($id_master);
        return response()->json($master);
    }

    // Update master. If stock_total increases -> generate additional assets. If decreases -> only allow if enough available units exist.
    public function update(Request $request, $id_master)
    {
        $master = AssetMaster::with('assets')->findOrFail($id_master);

        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'id_category' => 'nullable|string|exists:categories,id_category',
            'id_type' => 'nullable|string|exists:types,id_type',
            'stock_total' => 'nullable|integer|min:0'
        ]);

        DB::beginTransaction();
        try {
            if (isset($validated['stock_total'])) {
                $newTotal = (int) $validated['stock_total'];
                $currentTotal = (int) $master->stock_total;
                $availableUnits = $master->assets()->where('status','available')->count();

                if ($newTotal < $currentTotal) {
                    $delta = $currentTotal - $newTotal;
                    // can decrease only if there are at least delta available units to remove
                    if ($availableUnits < $delta) {
                        return response()->json(['error' => 'Cannot decrease stock_total: not enough available units to remove'], 400);
                    }
                    // remove delta available assets (choose newest available)
                    $assetsToDelete = $master->assets()->where('status','available')->limit($delta)->get();
                    foreach ($assetsToDelete as $a) {
                        $a->delete();
                    }
                    $master->stock_total = $newTotal;
                    $master->stock_available = max(0, $master->stock_available - $delta);
                } elseif ($newTotal > $currentTotal) {
                    $delta = $newTotal - $currentTotal;
                    for ($i=0; $i<$delta; $i++) {
                        Asset::create(['id_master' => $master->id_master, 'status'=>'available', 'condition'=>'good']);
                    }
                    $master->stock_total = $newTotal;
                    $master->stock_available += $delta;
                }
            }

            // update other fields
            foreach (['name','description','id_category','id_type'] as $f) {
                if (isset($validated[$f])) $master->$f = $validated[$f];
            }

            $master->save();
            DB::commit();
            return response()->json(['message' => 'Asset master updated', 'data'=>$master]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error'=>'Failed to update master','message'=>$e->getMessage()],500);
        }
    }

    // Delete master (cascade will remove assets)
    public function destroy($id_master)
    {
        $master = AssetMaster::findOrFail($id_master);
        $master->delete();
        return response()->json(['message'=>'Asset master deleted']);
    }

    // Change single asset unit status (e.g., maintenance)
    public function updateAssetStatus(Request $request, $id_asset)
    {
        $request->validate(['status' => 'required|in:available,borrowed,maintenance','condition'=>'nullable|in:good,damaged,lost']);
        $asset = Asset::findOrFail($id_asset);

        if ($request->has('status')) $asset->status = $request->status;
        if ($request->has('condition')) $asset->condition = $request->condition;
        $asset->save();

        // Keep stock_available consistent: recalc master
        $master = $asset->master;
        $master->stock_available = $master->assets()->where('status','available')->count();
        $master->save();

        return response()->json(['message'=>'Asset unit updated','data'=>$asset]);
    }
}
