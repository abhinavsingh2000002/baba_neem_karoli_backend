<?php

namespace App\Http\Controllers\Api\AdminController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Api\Traits\ValidationTrait;
use App\Models\User;
use App\Models\Order;
use App\Models\Product; 


class AdminDashboardController extends Controller
{
    use ValidationTrait;

    public function dashboard(Request $request)
    {
        $user = $this->validate_user($request->connection_id, $request->auth_code);
        if($user)
        {
            $distributor=User::where('role_id','=',2)->where('status','=',1)->count();
            $driver=User::where('role_id','=',3)->where('status','=',1)->count();
            $order = Order::whereDate('created_at', today())->count();
            $recentOrders = Order::select('orders.order_no','users.name as distributorName','orders.order_status')
            ->join('users','orders.user_id','=','users.id')
            ->latest('orders.order_date')->limit(5)->orderBy('orders.id','desc')->get();
            $product=Product::where('status','=',1)->count();
            return response()->json([
                'status' => 'success',
                'distributor' => $distributor,
                'driver' => $driver,
                'order' => $order,
                'product' => $product,
                'recentOrders' => $recentOrders,
                'message' => 'Dashboard Data retrieved successfully',
            ], 200); // HTTP 200 OK 
        }
        else {
            return response()->json([
                'status' => 'error',
                'message' => 'User not authenticated',
            ], 401); // HTTP 401 Unauthorized
        }
    }
}
