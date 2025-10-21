<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Type;

class TypeController extends Controller
{
    public function index() { return Type::orderBy('name')->get(); }

    public function store(Request $req)
    {
        $req->validate(['name'=>'required|string|max:100']);
        $c = Type::create(['name'=>$req->name]);
        return response()->json(['message'=>'Type created','data'=>$c],201);
    }

    public function show($id) { return Type::findOrFail($id); }

    public function update(Request $req, $id)
    {
        $c = Type::findOrFail($id);
        $req->validate(['name'=>'required|string|max:100']);
        $c->name = $req->name; $c->save();
        return response()->json(['message'=>'Updated','data'=>$c]);
    }

    public function destroy($id) { Type::findOrFail($id)->delete(); return response()->json(['message'=>'Deleted']); }
}
