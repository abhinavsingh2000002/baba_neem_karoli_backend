<?php

namespace App\Http\Controllers\Api\AdminController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Product;
use App\Models\MapProductPrice;
use App\Api\Traits\ValidationTrait;


class AdminMapProductController extends Controller
{   
    use ValidationTrait;

    public function mapProductPriceListing(Request $request)
    {
        $user = $this->validate_user($request->connection_id, $request->auth_code);
        if($user)
        {
            $query = MapProductPrice::select(
                'map_product_prices.id as mapProductPriceId',
                'map_product_prices.price as price',
                'products.product_name as productName',
                'products.product_no as productNo',
                'users.name as distributorName',
                'products.product_image as productImage'
            )
            ->join('products', 'map_product_prices.product_id', '=', 'products.id')
            ->join('users', 'map_product_prices.user_id', '=', 'users.id')
            ->where('map_product_prices.status', '=', 1)
            ->where('users.status', '=', 1);

            // Add search by distributor name or product name
            if ($request->has('search')) {
                $query->where(function($q) use ($request) {
                    $q->where('users.name', 'LIKE', '%' . $request->search . '%')
                      ->orWhere('products.product_name', 'LIKE', '%' . $request->search . '%');
                });
            }

            // Add distributor filter if distributor_id is provided
            if ($request->has('distributor_id')) {
                $query->where('map_product_prices.user_id', $request->distributor_id);
            }

            $mapProductPrice = $query->orderBy('map_product_prices.id', 'desc')->get();
            
            return response()->json([
                'status' => 'success',
                'mapProductPrice' => $mapProductPrice,
                'message' => 'Map product price listing retrieved successfully',
            ], 200);
        }
        else {
            return response()->json([
                'status' => 'error',
                'message' => 'User not authenticated',
            ], 401);
        }
    }

    public function distrubutorListing(Request $request)
    {  
        $user = $this->validate_user($request->connection_id, $request->auth_code);
        if($user)
        {
            $distributor=User::select('users.id as distributorId','users.name as distributorName')->where('role_id','=',2)->where('status','=',1)->get();
            return response()->json([
                'status' => 'success',
                'distributor' => $distributor,
                'message' => 'Distributor retrieved successfully',
            ], 200); // HTTP 200 OK
        }
        else {
            return response()->json([
                'status' => 'error',
                'message' => 'User not authenticated',
            ], 401); // HTTP 401 Unauthorized
        }
    }

    public function productListing(Request $request)
    {
        $user = $this->validate_user($request->connection_id, $request->auth_code);
        if($user)
        {
            // Get products that are not mapped for the specified distributor
            $product = Product::select('products.id as productId', 'products.product_name as productName')
                ->whereNotExists(function($query) use ($request) {
                    $query->select('map_product_prices.id')
                        ->from('map_product_prices')
                        ->whereColumn('map_product_prices.product_id', 'products.id')
                        ->where('map_product_prices.user_id', $request->distributor_id);
                })
                ->where('status', '=', 1)
                ->get();

            return response()->json([
                'status' => 'success',
                'product' => $product,
                'message' => 'Product retrieved successfully',
            ], 200);
        }
        else {
            return response()->json([
                'status' => 'error',
                'message' => 'User not authenticated',
            ], 401);
        }
    }

    public function addMapProductPrice(Request $request)
    {
        $user = $this->validate_user($request->connection_id, $request->auth_code);
        if($user)
        {
            $mapProductPrice= new MapProductPrice();
            $mapProductPrice->user_id=$request->distributor_id;
            $mapProductPrice->product_id=$request->product_id;
            $mapProductPrice->price=$request->price;
            $mapProductPrice->save();
            return response()->json([
                'status' => 'success',
                'mapProductPrice' => $mapProductPrice,
                'message' => 'Product price mapped successfully',
            ], 200);
        }
        else {
            return response()->json([
                'status' => 'error',
                'message' => 'User not authenticated',
            ], 401);
        }
    }

    public function updateProductPrice(Request $request)
    {
        $user = $this->validate_user($request->connection_id, $request->auth_code);
        if($user)
        {
            $mapProductPrice=MapProductPrice::find($request->map_product_price_id);
            $mapProductPrice->price=$request->price;
            $mapProductPrice->save();
            return response()->json([
                'status' => 'success',
                'mapProductPrice' => $mapProductPrice,
                'message' => 'Product price updated successfully',
            ], 200);
        }
        else {
            return response()->json([
                'status' => 'error',
                'message' => 'User not authenticated',
            ], 401);
        }
    }
}
