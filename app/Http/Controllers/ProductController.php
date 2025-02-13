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

    public function view_product(){
        $data = Product::latest()->paginate(9);
        return view('dashboard.products.view', compact('data'));
    }

    public function store_product(Request $request) {

        if($request->isMethod('post')) {
            $validated = $request->validate([
                'pro_name'      => 'required|unique:products|max:255',
                'price'         => 'required',
                'brand'         => 'required',
                'available'     => 'required',
                'img'           => 'required'
            ]);
            $pro_name   = strip_tags($request->pro_name);
            $price      = strip_tags($request->price);
            $brand      = strip_tags($request->brand);
            $available  = strip_tags($request->available);
            $details    = strip_tags($request->details);
            $img = $request->file('img');
            $gen        = hexdec(uniqid());
            $ex         = strtolower($img->getClientOriginalExtension());
            $name       = $gen . '.' . $ex;
            $location   = 'product/';
            $source     = $location . $name;
            $img->move($location, $name);
            $data = Product::insert([
                'pro_name'      => $pro_name,
                'price'         => $price,
                'brand'         => $brand,
                'img'           => $source,
                'available'     => $available,
                'details'       => $details,
                'created_at'    => Carbon::now()

            ]);
            if($data == true) {
                return redirect()->back()->with('msg', 'Product Add Success');
            } else {
                return redirect()->back()->with('msg', 'Product Not Add Success');
            }
        } else {
            return redirect()->route('login');
        }
    }

    public function edit_product($id) {
        $data = Product::findOrFail($id);
        $brand = Brand::all();
        return view("dashboard.products.edit", compact('data', 'brand'));
    }

    public function update_product(Request $request) {
        if($request->isMethod('post')) {
            // return $request->all();
            $validated = $request->validate([
                'pro_name'=>'required|max:255',
                'price'=>'required',
                'brand'=>'required',
                'available'=>'required',
                'details'=>'required'
            ], [
                'pro_name.required' => 'Product name is required',
                'price.unique' => 'Price is required',
                'brand.required' => 'Brand is required',
                'available.required' => 'Product stock is required',
                'details.required' => 'Details is required'
            ]);

            $pro_name = strip_tags($request->name);
            $price = strip_tags($request->price);
            $brand = strip_tags($request->brand);
            $available = strip_tags($request->available);
            $details    = strip_tags($request->details);
            $id = $request->id;
            $product = Product::findOrFail($id);

            if($request->hasFile('img')) {
                unlink($product->img);
                $img = $request->img;
                $gen = hexdec(uniqid());
                $ex = strtolower($img->getClientOriginalExtension());
                $name = $gen . '.' . $ex;
                $location = 'product/';
                $source = $location . $name;
                $img->move($location, $name);
                $product->$img = $source;
            }
            $product->pro_name = $pro_name;
            $product->price = $price;
            $product->brand = $brand;
            $product->available = $available;
            $product->details = $details;
            $product->save();
            return redirect()->back()->with('msg', 'Product updated.');
        } else {
            return redirect()->route('login');
        }
    }

    public function delete_product($id) {
        $data = Product::findOrFail($id);
        unlink($data->img);
        $data->delete();
        return redirect()->back()->with("msg", "Product Deleted");
    }
}
