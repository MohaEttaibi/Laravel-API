<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Brand;
use App\Models\Product;

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
}
