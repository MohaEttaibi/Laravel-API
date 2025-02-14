<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Brand;
use Carbon\Carbon;

class BrandController extends Controller
{
    public function add_brand(){
        return view('dashboard.brands.add');
    }

    public function store_brand(Request $request){
        // return $request->all();
        $validated = $request->validate([
            'name' => 'required|unique:brands|max:255',
            'img' => 'required'
        ], [
            'name.required' => 'Brand name is required',
            'name.unique' => 'This brand added before',
            'img.required' => 'Brand image is required'
        ]);

        $brand = strip_tags($request->name);
        $img = $request->file('img');
        $gen = hexdec(uniqid());
        $ex = strtolower($img->getClientOriginalExtension());
        $name = $gen . '.' . $ex;
        $location = 'brand/';
        $source = $location . $name;
        $img->move($location, $name);
        $brand = Brand::insert([
            'name' => $brand,
            'img' => $source,
            'created_at' => Carbon::now()
        ]);
        if ($brand == true){
            return redirect()->back()->with('msg', 'Brand Added Successfuly.');
        } else {
            return redirect()->back()->with('msg', 'Brand Not Added.');
        }
    }

    public function view_brand(){
        $data = Brand::latest()->paginate(10);
        return view('dashboard.brands.index', compact('data'));
    }

    public function edit_brand($id) {
        $data = Brand::findOrFail($id);
        return view("dashboard.brands.edit", compact('data'));
    }

    public function update_brand(Request $request) {
        $validated = $request->validate([
            'name' => 'required|unique:brands'
        ], [
            'name.required' => 'Brand name is required',
            'name.unique' => 'Name is taken'
        ]);

        $id = $request->id; 
        $name = strip_tags($request->name);
        $data = Brand::where('id', '=', $id)->first();
        $data->name = $name;
        if($request->hasFile('img')) {
            unlink($data->img);
            $img = $request->file('img');
            $gen = hexdec(uniqid());
            $ex = strtolower($img->getClientOriginalExtension());
            $name = $gen . '.' . $ex;
            $location = 'brand/';
            $source = $location . $name;
            $img->move($location, $name);
            $data->$img = $source;
        }
        $data->save();
        return redirect()->back()->with('msg', 'Brand updated.');
        // return $request->all();
    }

    public function delete_brand($id) {
        $brand = Brand::findOrFail($id);
        unlink($brand->img);
        $brand->delete();
        return redirect()->back();
    }
}
