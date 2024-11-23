<?php

namespace App\Http\Controllers\Api\AdminController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DriverTask;
use App\Models\User;
use App\Models\Order;
use Carbon\Carbon;
use App\Api\Traits\ValidationTrait;

class AdminDriverTaskController extends Controller
{
    use ValidationTrait;
    public function driverListing(Request $request)
    {  
        $user = $this->validate_user($request->connection_id, $request->auth_code);
        if($user)
        {
            $driver=User::select('users.id as driverId','users.name as driverName')->where('role_id','=',3)->get();
            return response()->json([
                'status' => 'success',
                'driver' => $driver,
                'message' => 'Driver retrieved successfully',
            ], 200); // HTTP 200 OK
        }
        else {
            return response()->json([
                'status' => 'error',
                'message' => 'User not authenticated',
            ], 401); // HTTP 401 Unauthorized
        }
    }

    public function driverListingForAssignTask(Request $request)
    {  
        $user = $this->validate_user($request->connection_id, $request->auth_code);
        if($user)
        {
            $driver=User::select('users.id as driverId','users.name as driverName')->where('role_id','=',3)->where('status','=',1)->get();
            return response()->json([
                'status' => 'success',
                'driver' => $driver,
                'message' => 'Driver retrieved successfully',
            ], 200); // HTTP 200 OK
        }
        else {
            return response()->json([
                'status' => 'error',
                'message' => 'User not authenticated',
            ], 401); // HTTP 401 Unauthorized
        }
    }

    public function driverTaskListing(Request $request)
    {
        $user = $this->validate_user($request->connection_id, $request->auth_code);
        if($user)
        {
            $query = DriverTask::select('driver.name as driverName','distributor.name as distributorName','driver_tasks.order_no','driver_tasks.task_alloted_date','driver_tasks.task_alloted_time','driver_tasks.status')
            ->join('users as driver','driver_tasks.driver_id','=','driver.id')
            ->join('users as distributor','driver_tasks.user_id','=','distributor.id');
            if($request->has('date'))
            {
                $query->whereDate('task_alloted_date', $request->date);
            }
            if($request->has('driver_id'))
            {
                $query->where('driver_id', $request->driver_id);
            }
            if($request->has('status'))
            {
                $query->where('driver_tasks.status', $request->status);
            }
            if($request->has('search'))
            {
                $query->where('driver_tasks.order_no', 'like', '%'.$request->search.'%')
                ->orWhere('driver.name', 'like', '%'.$request->search.'%')
                ->orWhere('distributor.name', 'like', '%'.$request->search.'%');
            }
            return response()->json([
                'status' => 'success',
                'data' => $query->get(),
                'message' => 'Driver task listing retrieved successfully',
            ]);
        }
        else {
            return response()->json([
                'status' => 'error',
                'message' => 'User not authenticated',
            ], 401); // HTTP 401 Unauthorized
        }
    }

    public function fetchOrderForAssignTask(Request $request)
    {
        $user = $this->validate_user($request->connection_id, $request->auth_code);
        if($user)
        {
            $order = Order::select('orders.id as orderId','orders.user_id as distributorId','orders.order_no','orders.order_date','orders.order_time','distributor.name as distributorName','distributor.address as distributorAddress')
            ->join('users as distributor','orders.user_id','=','distributor.id')
                ->where('orders.order_status', '=', 2)
                ->whereNotExists(function($query) {
                    $query->select('driver_tasks.id')
                        ->from('driver_tasks')
                        ->whereRaw('driver_tasks.order_id = orders.id')
                        ->whereIn('driver_tasks.status', [0, 1]);
                })
                ->get();
            
            return response()->json([
                'status' => 'success',
                'order' => $order,
                'message' => 'Order retrieved successfully',
            ]);
        }
    }   


    public function assignTask(Request $request)
    {
        $user = $this->validate_user($request->connection_id, $request->auth_code);
        if($user)
        {   $order = Order::find($request->order_id);
            $driverTask = new DriverTask();
            $driverTask->order_id = $request->order_id;
            $driverTask->order_no = $order->order_no;
            $driverTask->driver_id = $request->driver_id;
            $driverTask->user_id = $order->user_id;
            $driverTask->status = 0;
            $driverTask->task_alloted_date = Carbon::now()->toDateString();
            $driverTask->task_alloted_time = Carbon::now()->toTimeString();
            $driverTask->save();
            return response()->json([
                'status' => 'success',
                'driverTask' => $driverTask,
                'message' => 'Task assigned successfully',
            ]);
        }
        else {
            return response()->json([
                'status' => 'error',
                'message' => 'User not authenticated',
            ], 401); // HTTP 401 Unauthorized
        }
    }
}
