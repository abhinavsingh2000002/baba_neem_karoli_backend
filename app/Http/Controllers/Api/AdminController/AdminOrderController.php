<?php

namespace App\Http\Controllers\Api\AdminController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Api\Traits\ValidationTrait;
use App\Models\User;
use App\Models\Bill;
use App\Models\Order;
use App\Models\OrderDetail;

class AdminOrderController extends Controller
{
    use ValidationTrait;

    public function distributorList(Request $request)
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

    public function orderListing(Request $request)
    {
        // Validate user using connection ID and auth code
        $user = $this->validate_user($request->connection_id, $request->auth_code);

        if ($user) {
            // Initialize the query for orders
            $query = Order::select('orders.*','users.name as distributorName')->join('users','orders.user_id','=','users.id');

            // Check if a date is provided in the request
            if ($request->has('date')) {
                // Validate and format the date if needed
                $date = $request->input('date');
                $query->whereDate('order_date', $date);
            }

            // Filter by distributor_id if provided
            if ($request->has('distributor_id')) {    
                $query->where('orders.user_id', $request->distributor_id);
            }

            // Retrieve the orders based on the query
            $orders = $query->orderBy('orders.id','desc')->get();

            // Add the count of ordered items to each order
            $orders->map(function ($order) {
                // Count the number of items in OrderDetail for the order's order_id
                $order->noOfOrderedItems = OrderDetail::where('order_id', $order->id)->count();
                return $order;
            });

            return response()->json([
                'status' => 'success',
                'orders' => $orders,
                'message' => 'Orders retrieved successfully',
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
            $order=Order::select('orders.*','users.name as distributorName')->join('users','orders.user_id','=','users.id')->find($request->order_id);
            $orderDetails=OrderDetail::where('order_id',$request->order_id)->get();
            return response()->json([
                'status' => 'success',
                'ordersNo' => $order->order_no,
                'orderDate'=>$order->order_date .',' . date('h:i A', strtotime($order->order_time)),
                'quantity'=>count($orderDetails),
                'totalAmount'=>$order->total_amount,
                'Staus'=>$order->order_status,
                'distributorName'=>$order->distributorName,
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
