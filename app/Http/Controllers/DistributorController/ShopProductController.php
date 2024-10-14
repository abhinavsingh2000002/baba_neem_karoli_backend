<?php

namespace App\Http\Controllers\DistributorController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\MapProductPrice;
use Illuminate\Support\Facades\Auth;
use Session;
use App\Models\ShoppingCart;


class ShopProductController extends Controller
{
    public function index()
    {
        return view('Distributor.Product.product_listing');
    }

    public function listing(Request $request)
    {
        if($request->search){
            $perPage = 8;
            $currentPage = $request->get('page', 1);
            $productsQuery=MapProductPrice::join('products','map_product_prices.product_id','products.id')
            ->where('user_id',Auth::user()->id)->where('products.product_name','LIKE','%'.$request->search.'%')->paginate($perPage);
            $products = $productsQuery->items();
            $totalPages = $productsQuery->lastPage();

            return response()->json([
                'products' => $products,
                'currentPage' => $currentPage,
                'totalPages' => $totalPages,
            ]);
        }
        $perPage = 8;
        $currentPage = $request->get('page', 1);
        $productsQuery=MapProductPrice::join('products','map_product_prices.product_id','products.id')
        ->where('user_id',Auth::user()->id)->where('products.status',1)->where('map_product_prices.status',1)->paginate($perPage);
        $products = $productsQuery->items();
        $totalPages = $productsQuery->lastPage();

        return response()->json([
            'products' => $products,
            'currentPage' => $currentPage,
            'totalPages' => $totalPages,
        ]);
    }

    public function productDetails($id)
    {
        $product_detail=MapProductPrice::join('products','map_product_prices.product_id','products.id')
        ->where('map_product_prices.id',$id)->where('products.status',1)->where('map_product_prices.status',1)->first();
        // dd($product_detail);
        return view('Distributor.Product.product_listing_detail')->with(['product_detail'=>$product_detail]);
    }

}
