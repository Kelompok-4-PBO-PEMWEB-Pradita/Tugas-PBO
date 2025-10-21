<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;

class CategoryController extends Controller
{
    public function index() { return Category::orderBy('name')->get(); }

    public function store(Request $req)
    {
        $req->validate(['name'=>'required|string|max:100']);
        $c = Category::create(['name'=>$req->name]);
        return response()->json(['message'=>'Category created','data'=>$c],201);
    }

    public function show($id) { return Category::findOrFail($id); }

    public function update(Request $req, $id)
    {
        $c = Category::findOrFail($id);
        $req->validate(['name'=>'required|string|max:100']);
        $c->name = $req->name; $c->save();
        return response()->json(['message'=>'Updated','data'=>$c]);
    }

    public function destroy($id) { Category::findOrFail($id)->delete(); return response()->json(['message'=>'Deleted']); }
}
