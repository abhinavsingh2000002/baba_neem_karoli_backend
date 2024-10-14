<?php

namespace App\Http\Controllers\DistributorController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ShoppingCart;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class CartController extends Controller
{

    public function index()
    {
        $cart_data=ShoppingCart::join('products','shopping_carts.product_id','=','products.id')->join('map_product_prices','products.id','=','map_product_prices.product_id')
        ->where('map_product_prices.user_id','=',Auth::user()->id)->where('shopping_carts.user_id','=',Auth::user()->id)
        ->where('map_product_prices.status',1)->where('products.status',1)->get();
        // dd($cart_data);
        return view('Distributor.Product.add_to_cart')->with(['cart_data'=>$cart_data]);
    }
    public function addToCart(Request $request)
    {
        // dd($request->all());
        $productId = $request->input('product_id');
        $quantity = $request->input('quantity');
        $exist_detail=ShoppingCart::where('product_id',$request->product_id)->where('user_id',Auth::user()->id)->first();
        if($exist_detail)
        {
            $update_shoping_cart=ShoppingCart::find($exist_detail->id);
            $update_shoping_cart->quantity=$request->quantity;
            $update_shoping_cart->save();
            return response()->json(['update' => true, 'message' => 'Product Quantity Updated successful at cart']);
        }
        $shopping_cart=new ShoppingCart();
        $shopping_cart->product_id=$request->product_id;
        $shopping_cart->quantity=$request->quantity;
        $shopping_cart->user_id=Auth::user()->id;
        $shopping_cart->save();
        return response()->json(['success' => true, 'message' => 'Product added to cart']);
    }

    public function listing()
    {
        $cart_data=ShoppingCart::select('shopping_carts.id as cart_id','shopping_carts.quantity','products.*','map_product_prices.*')
        ->join('products','shopping_carts.product_id','=','products.id')
        ->join('map_product_prices','products.id','=','map_product_prices.product_id')
        ->where('map_product_prices.user_id','=',Auth::user()->id)->where('shopping_carts.user_id','=',Auth::user()->id)
        ->where('map_product_prices.status',1)->where('products.status',1)
        ->get();
        $user_data=User::where('id','=',AUth::user()->id)->first();
        return response()->json(['data'=>$cart_data,'user_data'=>$user_data]);
    }

    public function delete(Request $request){

        $deleteCartItem=ShoppingCart::where('id',$request->id)->delete();
        if($deleteCartItem){
            return response()->json(['success'=>true,'msg'=>"Cart Item deleted successfully"]);
        }
        else{
            return response()->json(['success'=>true ,'msg'=>'error while deleting cart item']);
        }
    }

}
