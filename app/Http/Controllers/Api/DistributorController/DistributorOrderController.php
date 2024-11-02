<?php

namespace App\Http\Controllers\Api\DistributorController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Api\Traits\ValidationTrait;
use App\Models\Order;
use App\Models\OrderDetail;

class DistributorOrderController extends Controller
{
    use ValidationTrait;
    public function orderListing(Request $request)
    {
         // Validate user using connection ID and auth code
         $user = $this->validate_user($request->connection_id, $request->auth_code);

         if ($user) {
             // Initialize the query for bills
             $query = Order::where('user_id', '=', $user);

             // Check if a date is provided in the request
             if ($request->has('date')) {
                 // Validate and format the date if needed
                 $date = $request->input('date');
                 // Assuming bill_date is stored as a date
                 $query->whereDate('order_date', $date);
             }

             // Retrieve the bills based on the query
             $orders = $query->get();

             return response()->json([
                 'status' => 'success',
                 'orders' => $orders,
                 'message' => 'orders retrieved successfully',
             ], 200); // HTTP 200 OK
         } else {
             return response()->json([
                 'status' => 'error',
                 'message' => 'User not authenticated',
             ], 401); // HTTP 401 Unauthorized
         }
    }

    public function orderDetailListing(Request $request)
    {
        $user = $this->validate_user($request->connection_id, $request->auth_code);
        if($user)
        {
            $order=Order::find($request->order_id);
            $orderDetails=OrderDetail::where('order_id',$request->order_id)->get();
            return response()->json([
                'status' => 'success',
                'ordersNo' => $order->order_no,
                'orderDate'=>$order->order_date .',' . date('h:i A', strtotime($order->order_time)),
                'quantity'=>count($orderDetails),
                'totalAmount'=>$order->total_amount,
                'Staus'=>$order->order_status,
                'orderDetails'=>$orderDetails,
                'message' => 'ordersDetails retrieved successfully',
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
