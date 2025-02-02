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
        // $request->all();
        // return view('dashboard.products.add');
    }
}
