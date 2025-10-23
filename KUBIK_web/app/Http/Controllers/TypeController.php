<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Category;
use App\Models\Type;
use App\Models\AssetMaster;

class TypeController extends Controller
{
    /**
     * Show all types
     */
    public function index()
    {
        $Types = type::select('id_type', 'name', 'created_at')->get();

        return response()->json([
            'message' => 'List of all types',
            'data' => $Types
        ]);
    }

    /**
     * Show single type
     */
    public function show($id)
    {
        $type = type::find($id);

        if (!$type) {
            return response()->json(['message' => 'type not found'], 404);
        }

        return response()->json(['data' => $type]);
    }

    /**
     * Create new type
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $type = type::create([
            'name' => $request->name
        ]);

        return response()->json([
            'message' => 'type created successfully',
            'data' => $type
        ], 201);
    }

    /**
     * Update type
     */
    public function update(Request $request, $id)
    {
        $type = type::find($id);

        if (!$type) {
            return response()->json(['message' => 'type not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $type->update([
            'name' => $request->name
        ]);

        return response()->json([
            'message' => 'type updated successfully',
            'data' => $type
        ]);
    }

    /**
     * Delete type (only if unused)
     */
    public function destroy($id)
    {
        $type = type::find($id);

        if (!$type) {
            return response()->json(['message' => 'type not found'], 404);
        }

        // Smart Logic: Prevent deletion if still used
        $usedInType = Type::where('id_type', $id)->exists();
        $usedInMaster = AssetMaster::where('id_type', $id)->exists();

        if ($usedInType || $usedInMaster) {
            return response()->json([
                'message' => 'Cannot delete type. It is still used by another record.'
            ], 400);
        }

        $type->delete();

        return response()->json(['message' => 'type deleted successfully']);
    }
}
