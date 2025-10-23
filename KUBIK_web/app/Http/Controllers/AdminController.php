<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\Admin;

class AdminController extends Controller
{
    /**
     * Get all admins
     */
    public function index()
    {
        $admins = Admin::select('id_admin', 'name', 'email', 'created_at')->get();
        return response()->json(['data' => $admins]);
    }

    /**
     * Get single admin by ID
     */
    public function show($id)
    {
        $admin = Admin::find($id);

        if (!$admin) {
            return response()->json(['message' => 'Admin not found'], 404);
        }

        return response()->json(['data' => $admin]);
    }

    /**
     * Register new admin (optional – can be used by super admin)
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'      => 'required|string|max:100',
            'email'     => 'required|email|unique:admins,email',
            'password'  => 'required|min:6'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $admin = Admin::create([
            'name'      => $request->name,
            'email'     => $request->email,
            'password'  => bcrypt($request->password)
        ]);

        return response()->json([
            'message' => 'Admin registered successfully',
            'data'    => $admin
        ], 201);
    }

    /**
     * Login admin
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'     => 'required|email',
            'password'  => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $admin = Admin::where('email', $request->email)->first();

        if (!$admin || !Hash::check($request->password, $admin->password)) {
            return response()->json(['message' => 'Invalid admin credentials'], 401);
        }

        return response()->json([
            'message' => 'Login successful',
            'data'    => [
                'id_admin' => $admin->id_admin,
                'name'     => $admin->name,
                'email'    => $admin->email
            ]
        ]);
    }
}
