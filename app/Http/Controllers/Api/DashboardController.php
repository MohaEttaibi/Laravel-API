<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Brand;
use App\Models\Product;
use App\Models\Favorite;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function home() {
        $brand =  Brand::all();
        $product =  Product::where('available', '=', 1)->get();
        return response()->json([
            'status' => true,
            'message' => 'Home Page',
            'brand' => $brand,
            'product' => $product,
        ], 200);
    }

    public function products_by_brand($brand) {
        $brands = Brand::where('id', '=', $brand)->first();
        $product = Product::where([
            ['available', '=', 1],
            ['brand', '=', $brands->id]
        ])->get();
        return response()->json([
            'status' => true,
            'message' => 'Products By Brand Page',
            'brand' => $brand,
            'products' => $product,
        ], 200);
    }

    public function products_view($id) {
        $data = Product::findOrFail($id);
        return response()->json([
            'status' => true,
            'message' => 'Product Details',
            'product' => $data,
        ], 200);
    }

    public function filters($filter) {
        if ($filter == 'low') {
            $data = Product::orderBy('price', 'asc')->get();
        } else if ($filter == 'high'){
            $data = Product::orderBy('price', 'desc')->get();
        } else if ($filter == 'new'){
            $data = Product::latest()->get();
        } else if ($filter == 'old') {
            $data = Product::all();
        }
        return response()->json([
            'status' => true,
            'message' => 'Product Filters',
            'product' => $data,
        ], 200);
    }

    public function filters_by_brand($brand, $filter) {
        $brand = Brand::findOrFail($brand);
        if ($filter == 'low') {
            $data = Product::where('brand', '=', $brand)->orderBy('price', 'asc')->get();
        } else if ($filter == 'high') {
            $data = Product::where('brand', '=', $brand)->orderBy('price', 'desc')->get();
        } else if ($filter == 'new') {
            $data = Product::where('brand', '=', $brand)->latest()->get();
        } else if ($filter == 'old') {
            $data = Product::where('brand', '=', $brand)->all()->get();
        }
        return response()->json([
            'status' => true,
            'message' => 'Filters By Brand',
            'product' => "Brand is $brand, Filter is $data",
        ], 200);
    }

    public function add_favorite($product_id) {
        $user = auth('sanctum')->user();
        $check = Favorite::where([
            ['user_id', '=', $user->id],
            ['product_id', '=', $product_id]
        ])->first();
        if(isset($check)) {
            return response()->json([
                'status' => true,
                'message' => 'This item is add to favorite before',
                'product' => $user,
            ]);    
        } else {
            $data = new Favorite;
            $data->product_id = $product_id;
            $data->user_id = $user->id;
            $data->created_at = Carbon::now();
            $data->save();

            return response()->json([
                'status' => true,
                'message' => 'This item is add to favorite',
                'product' => $data,
            ], 200);
        }
    }

    public function fetch_favorite() {
        $user = auth('sanctum')->user();
        $data = DB::table('favorites')->where('user_id', '=', $user->id)->join('products', 'favorites.product_id', 'products.id')->select('products.*')->get();
        return response()->json([
            'status' => true,
            'message' => 'Fetch Items',
            'product' => $data,
        ], 200);
    }

    public function remove_favorite ($id) {
        $user = auth('sanctum')->user();
        $data = Favorite::where([
            ['user_id', '=', $user->id],
            ['product_id', '=', $id],
        ])->delete();
        if ($data == true) {
            return response()->json([
                'status' => true,
                'message' => 'Item Removed',
                'data' => $data,
            ]);
        } else {
            return response()->json([
                'status' => true,
                'message' => 'Error, item can\'t be removed',
            ]);    
        }   
    }
}
