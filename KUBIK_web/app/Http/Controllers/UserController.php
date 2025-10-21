<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index() { return User::orderByDesc('created_at')->get(); }

    public function store(Request $req) {
        $req->validate(['name'=>'required','email'=>'required|email|unique:users,email','password'=>'required|min:6']);
        $user = User::create([
            'name'=>$req->name,'email'=>$req->email,'phone'=>$req->phone??null,
            'password'=>Hash::make($req->password)
        ]);
        return response()->json(['message'=>'User created','data'=>$user],201);
    }

    public function show($id){ return User::findOrFail($id); }

    public function update(Request $req,$id){
        $user = User::findOrFail($id);
        $req->validate(['email'=>"email|unique:users,email,{$id},id_user"]);
        if ($req->has('name')) $user->name = $req->name;
        if ($req->has('email')) $user->email = $req->email;
        if ($req->has('phone')) $user->phone = $req->phone;
        if ($req->has('password')) $user->password = Hash::make($req->password);
        $user->save();
        return response()->json(['message'=>'User updated','data'=>$user]);
    }

    public function destroy($id){ User::findOrFail($id)->delete(); return response()->json(['message'=>'User deleted']); }
}
