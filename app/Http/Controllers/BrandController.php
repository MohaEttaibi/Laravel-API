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

    public function view_brand(){
        return view('dashboard.brands.index');
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
            return redirect()->back()-with('msg', 'Brand Added Successfuly.');
        } else {
            return redirect()->back()->with('msg', 'Brand Not Added.');
        }
    }
}
