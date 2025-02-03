<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Brand;
use Carbon\Carbon;

class ProductController extends Controller
{
    public function add_product(){
        $brand = Brand::all();
        return view('dashboard.products.add', compact('brand'));
    }

    public function store_product(Request $request){
        if($request->isMethod('post')){
            $validated = $request->validate([
                'pro_name'=>'required|unique:products|max:255',
                'price'=>'required',
                'brand'=>'required',
                'available'=>'required',
                'img'=>'required'
            ], [
                'pro_name.required' => 'Product name is required',
                'price.unique' => 'Price is required',
                'brand.required' => 'Brand is required',
                'available.required' => 'Product stock is required',
                'img.required' => 'Product image is required',
            ]);

            $pro_name = strip_tags($request->name);
            $price = strip_tags($request->price);
            $brand = strip_tags($request->brand);
            $available = strip_tags($request->available);
            $img = $request->file('img');
            $gen = hexdec(uniqid());
            $ex = strtolower($img->getClientOriginalExtension());
            $name = $gen . '.' . $ex;
            $location = 'product/';
            $source = $location . $name;
            $img->move($location, $name);
            $data = Product::insert([
                'pro_name' => $pro_name,
                'price' => $price,
                'brand' => $brand,
                'available' => $available,
                'img' => $source,
                'created_at' => Carbon::now()
            ]);

            if($data == true) {
                return redirect()->back()->with('msg', 'Product addedd successfuly.');
            } else {
                return redirect()->back()->with('msg', 'Product not addedd');
            }
            return $request->all();
        } else {
            return redirect()->route('login');
        }
    }
}
