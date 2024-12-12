<?php

namespace App\Http\Controllers\Api\AdminController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Product;
use App\Api\Traits\ValidationTrait;

class AdminReportController extends Controller
{
    use ValidationTrait;

    public function reportListing(Request $request)
    {
        $user = $this->validate_user($request->connection_id, $request->auth_code);
        if($user)
        {
            $all_product = Product::select('products.product_no', 'products.product_name', 'products.product_quantity as productWeight')
            ->orderBy(\DB::raw('ISNULL(products.display_order), products.display_order'), 'asc')->get();
            $order = Order::with(['orderDetails', 'user'])
                ->join('users', 'orders.user_id', '=', 'users.id')
                ->where('order_date', '=', $request->date)
                ->orderBy(\DB::raw('ISNULL(users.display_order), users.display_order'), 'asc')
                ->select('orders.*')
                ->get();

            return response()->json([
                'status' => 'success',
                'products' => $all_product,
                'orders' => $order,
                'message' => 'report retrieved successfully',
            ], 200);
        }
        else{
            return response()->json([
                'status' => 'error',
                'message' => 'User not authenticated',
            ], 401); // HTTP 401 Unauthorized
        }
    }


    // public function reportListing(Request $request)
    // {
    //     $user = $this->validate_user($request->connection_id, $request->auth_code);
    //     if($user)
    //     {
    //         $all_product=Product::select('products.product_no','products.product_name','products.product_quantity as productWeight')->where('status',1)->get();
    //         $order = Order::with(['orderDetails', 'user'])
    //         ->where('order_date', '=', $request->date)
    //         ->get();
    //         return response()->json([
    //             'status' => 'success',
    //             'products' => $all_product,
    //             'orders'=>$order,
    //             'message' => 'report retrieved successfully',
    //         ], 200); // HTTP 200 OK
    //     }
    //     else{
    //         return response()->json([
    //             'status' => 'error',
    //             'message' => 'User not authenticated',
    //         ], 401); // HTTP 401 Unauthorized
    //     }
    // }
}
