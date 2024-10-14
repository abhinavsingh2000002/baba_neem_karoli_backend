<?php

namespace App\Http\Controllers\DistributorController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ShoppingCart;
use Illuminate\Support\Facades\Auth;

class OrderPlacedController extends Controller
{
    public function oderPlaced(Request $request)
    {
        $total_order=ShoppingCart::select('shopping_carts.quantity','products.*')->join('products','shopping_carts.product_id','=','products.id')->where('user_id',Auth::user()->id)->where('products.status','=',1)->get();
        dd($total_order);
    }
}
