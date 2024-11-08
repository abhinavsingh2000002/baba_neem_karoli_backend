<?php

namespace App\Http\Controllers\Api\DriverController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Product;
use App\Api\Traits\ValidationTrait;

class DriverReportController extends Controller
{
    use ValidationTrait;

    public function reportListing(Request $request)
    {
        $user = $this->validate_user($request->connection_id, $request->auth_code);
        if($user)
        {
            $all_product=Product::select('products.product_no','products.product_name','products.product_quantity as productWeight')->where('status',1)->get();
            $order = Order::with(['orderDetails', 'user'])
            ->where('order_date', '=', $request->date)
            ->where('order_status',2)
            ->get();
            return response()->json([
                'status' => 'success',
                'products' => $all_product,
                'orders'=>$order,
                'message' => 'Distributor retrieved successfully',
            ], 200); // HTTP 200 OK
        }
        else{
            return response()->json([
                'status' => 'error',
                'message' => 'User not authenticated',
            ], 401); // HTTP 401 Unauthorized
        }
    }
}
