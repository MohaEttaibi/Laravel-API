<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Brand;
use App\Models\Product;
use App\Models\Favorite;
use App\Models\Cart;
use App\Models\PhoneVerify;
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

    public function add_cart (Request $request) {
        if($request->isMethod('post')) {
            $data = $request->validate([
                'product' => 'required',
                'price'     => 'required',
                'quantity'  => 'required'
            ]);          
            $user = auth('sanctum')->user();
            $product = strip_tags($data['product']);
            $price = strip_tags($data['price']);
            $quantity = strip_tags($data['quantity']);
            $check = Cart::where([
                ['user_id', '=', $user->id],
                ['product', '=', $product]
            ])->first();
            if(isset($check)){
                return response()->json([
                    'status' => true,
                    'message' => 'Already Added to cart.',
                ], 200);
            } else {
                $data = Cart::insert([
                    'user_id' => $user->id,
                    'product' => $product,
                    'price' => $price,
                    'quantity' => $quantity,
                    'created_at' => Carbon::now()
                ]);
                return response()->json([
                    'status' => true,
                    'message' => 'Product was added to cart.',
                ], 200);
            }
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Not Found',
            ], 404);
        }
    }

    public function cart () {
        $user = auth('sanctum')->user();
        $data = DB::table('carts')
        ->where('user_id', '=', $user->id)
        ->join('products', 'carts.product', 'products.id')
        ->select('carts.id', 'carts.price', 'carts.quantity', 'carts.created_at', 'products.pro_name', 'products.img')
        ->latest()
        ->paginate(10);
        return response()->json([
            'status' => true,
            'message' => 'Get All Carts',
            'user' => $data
        ], 200);
    }

    public function remove_cart ($id) {
        $user = auth('sanctum')->user();
        $check = Cart::where([
            'user_id' => $user->id,
            'product' => $id
        ])->first();
        if(isset($check)) {
            $check->delete(); 
            return response()->json([
                'status' => true,
                'message' => 'Product removed from cart',
                'user' => $check
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Product not dound in cart',
            ], 404);
        }
    }

    public function remove_cart_all() {
        $user = auth('sanctum')->user();
        $data = Cart::where('user_id', '=', $user->id)->delete();
        if($data > 0) {
            return response()->json([
                'status' => true,
                'message' => 'all products removed from cart'
            ], 200);
        }
        return response()->json([
            'status' => true,
            'message' => 'No products found in your cart'
        ], 404);
    }

    public function phone_verify(Request $request) {
        if($request->isMethod('post')) {
            $data = $request->validate([
                'phone' => 'required|numeric'
            ]);
            $user  = auth('sanctum')->user();
            $phone = substr($data['phone'], 1);
        
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'https://api.authentica.sa/api/sdk/v1/sendOTP');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Accept: application/json',
                'X-Authorization: $2y$10$FtJC93GNzLOvDW5zbWn2eer.qVQURhK29wIzsQbc38J/fy1C4Q5L6',
                'Content-Type: application/json',
            ]);
            curl_setopt($ch, CURLOPT_POSTFIELDS, "{\n    \"phone\":\"+966$phone\",\n    \"method\":\"sms\",\n    \"sender_name\": \"\",\n    \"number_of_digits\": 4,\n    \"otp_format\": \"numeric\",\n    \"is_fallback_on\": 0 \n}");
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            $response = curl_exec($ch);
            curl_close($ch);
            $res = json_decode($response, true);
            return response()->json($res);
            if($res["success"] == true) { 
                PhoneVerify::where('user_id', '=', $user->id)->delete();
                PhoneVerify::insert([
                    'user_id'   => $user->id,
                    'phone'     => $data['phone'],
                    'created_at'    => Carbon::now()
                ]);
                return response()->json([
                    'status'    => true,
                    'message'   => 'The OTP Sent To Your Phone Number',
                ], 200);
            } else {
                return response()->json([
                    'status'    => False,
                    'message'   => 'There is Wrong Please Try Again',
                ], 200);
            }
        } else {
            return response()->json([
                'status' => true,
                'message' => 'phone not found'
            ], 404);
        }
    }
}
