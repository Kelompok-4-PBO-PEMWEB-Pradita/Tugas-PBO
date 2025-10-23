<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\User;

class UserController extends Controller
{
    /**
     * Get all users
     */
    public function index()
    {
        $users = User::select('id_user', 'name', 'email', 'phone_number', 'created_at')->get();
        return response()->json(['data' => $users]);
    }

    /**
     * Get single user by ID
     */
    public function show($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        return response()->json(['data' => $user]);
    }

    /**
     * Register new user
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'          => 'required|string|max:100',
            'email'         => 'required|email|unique:users,email',
            'password'      => 'required|min:6',
            'phone_number'  => 'nullable|string|max:20'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::create([
            'name'          => $request->name,
            'email'         => $request->email,
            'password'      => bcrypt($request->password),
            'phone_number'  => $request->phone_number
        ]);

        return response()->json([
            'message' => 'Registration successful',
            'data'    => $user
        ], 201);
    }

    /**
     * Login user
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

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        return response()->json([
            'message' => 'Login successful',
            'data'    => [
                'id_user' => $user->id_user,
                'name' => $user->name,
                'email' => $user->email
            ]
        ]);
    }
}
