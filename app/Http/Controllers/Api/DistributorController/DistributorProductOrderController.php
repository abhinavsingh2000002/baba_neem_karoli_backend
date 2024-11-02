<?php

namespace App\Http\Controllers\Api\DistributorController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Api\Traits\ValidationTrait;
use App\Models\Product;
use App\Models\MapProductPrice;
use App\Models\ShoppingCart;

class DistributorProductOrderController extends Controller
{
    use ValidationTrait;
    public function productListing(Request $request)
    {
        $user = $this->validate_user($request->connection_id, $request->auth_code);

        if ($user) {
            $products = MapProductPrice::select('products.*','map_product_prices.price')
                ->join('products','map_product_prices.product_id','=','products.id')
                ->where('map_product_prices.user_id','=',$user)
                ->where('products.status','=', 1)->get();

            if ($products) {
                return response()->json([
                    'status' => 'success',
                    'products' => $products,
                    'message' => 'Products retrieved successfully',
                ], 200); // HTTP 200 OK
            }
        }

        return response()->json([
            'status' => 'error',
            'message' => 'User not authenticated',
        ], 401); // HTTP 401 Unauthorized
    }

    public function addToCart(Request $request)
    {
        $user = $this->validate_user($request->connection_id, $request->auth_code);

        if ($user) {
            $existingProduct = ShoppingCart::where('product_id', $request->product_id)
                                        ->where('user_id', $request->user_id)
                                        ->first();

            if ($existingProduct) {
                $shoppingCart = ShoppingCart::find($existingProduct->id);
                $shoppingCart->quantity = $request->quantity;
                $shoppingCart->save();

                return response()->json([
                    'status' => 'success',
                    'message' => 'Product quantity updated successfully',
                ], 200); // HTTP 200 OK
            } else {
                $shoppingCart = new ShoppingCart();
                $shoppingCart->quantity = $request->quantity;
                $shoppingCart->user_id = $user;
                $shoppingCart->product_id = $request->product_id;

                if ($shoppingCart->save()) {
                    return response()->json([
                        'status' => 'success',
                        'message' => 'Product added to cart successfully',
                    ], 200); // HTTP 200 OK
                } else {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'An error occurred while adding the product to the cart.',
                    ], 500); // HTTP 500 Internal Server Error
                }
            }
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'User not authenticated.',
            ], 401); // HTTP 401 Unauthorized
        }
    }

    public function cartProduct(Request $request)
    {
        // Validate user authentication
        $user = $this->validate_user($request->connection_id, $request->auth_code);

        if ($user) {
            // Fetch cart products for the authenticated user
            $cartProducts = ShoppingCart::
            select('products.*','map_product_prices.price','shopping_carts.quantity','shopping_carts.id as shopping_cart_id')
            ->join('products','shopping_carts.product_id','=','products.id')
           ->join('map_product_prices','products.id','=','map_product_prices.product_id')->where('products.status','=',1)->where('shopping_carts.user_id', '=', $user)
           ->where('map_product_prices.user_id','=',$user)->where('products.status','=',1)->get();

            return response()->json([
                'status' => 'success',
                'cartProducts' => $cartProducts,
                'message' => 'Cart Products retrieved successfully',
            ], 200); // HTTP 200 OK
        } else {
            // User is not authenticated
            return response()->json([
                'status' => 'error',
                'message' => 'User not authenticated.',
            ], 401); // HTTP 401 Unauthorized
        }
    }

    public function removeCartProduct(Request $request)
    {
        $user = $this->validate_user($request->connection_id, $request->auth_code);

        if ($user) {
            // Find the shopping cart by ID and delete it
            $shoppingCart = ShoppingCart::find($request->shopping_cart_id);

            if ($shoppingCart) {
                $shoppingCart->delete();
                return response()->json([
                    'status' => 'success',
                    'message' => 'Shopping cart Product deleted successfully.'
                ], 200); // HTTP status code 200 OK
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Shopping cart Product not Found.'
                ], 404); // HTTP status code 404 Not Found
            }
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'User not authenticated.'
            ], 401); // HTTP status code 401 Unauthorized
        }
    }
}
