<?php

namespace App\Http\Controllers\Api\DriverController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Api\Traits\ValidationTrait;
use App\Models\DriverTask;
use App\Models\OrderDetail;
use Carbon\Carbon;
use App\Models\Order;
use App\Models\Cred;

class DriverAllotedTaskController extends Controller
{
    use ValidationTrait;

    public function allotedTaskListing(Request $request)
    {
         // Validate user using connection ID and auth code
         $user = $this->validate_user($request->connection_id, $request->auth_code);

         if ($user) {
             // Initialize the query for bills
             $query = DriverTask::select('driver_tasks.id','users.name as distributorName','users.address as distributorAddress','driver_tasks.task_alloted_date','driver_tasks.task_alloted_time','driver_tasks.status','driver_tasks.order_no','driver_tasks.order_id')->join('users','driver_tasks.user_id','=','users.id')->where('driver_id', '=', $user);

             // Check if a date is provided in the request
             if ($request->has('date')) {
                 // Validate and format the date if needed
                 $date = $request->input('date');
                 // Assuming bill_date is stored as a date
                 $query->whereDate('task_alloted_date', $date);
             }

             // Retrieve the bills based on the query
             $driverTasks = $query->get();

             return response()->json([
                 'status' => 'success',
                 'bills' => $driverTasks,
                 'message' => 'Driver Tasks retrieved successfully',
             ], 200); // HTTP 200 OK
         } else {
             return response()->json([
                 'status' => 'error',
                 'message' => 'User not authenticated',
             ], 401); // HTTP 401 Unauthorized
         }
    }

    public function allotedTaskApprove(Request $request)
    {
        // Validate user using connection ID and auth code
        $user = $this->validate_user($request->connection_id, $request->auth_code);
        if ($user) {
            try {
                $driver_task = DriverTask::findOrFail($request->task_id);
                $all_orders = OrderDetail::where('order_id', $driver_task->order_id)->get();
                $totalbox = 0;
                foreach ($all_orders as $order) {
                    $totalbox += $order->product_quantity;
                }
                $totalbox = floor($totalbox);
                if ($driver_task) {
                    // Update driver task status and completion date/time
                    $driver_task->status = 1;
                    $driver_task->task_completed_date = Carbon::now()->toDateString();
                    $driver_task->task_completed_time = Carbon::now()->toTimeString();
                    $driver_task->save();

                    // Update order status and delivery date/time
                    $order = Order::findOrFail($driver_task->order_id);
                    $order->order_status = 3;
                    $order->order_deliverd_date = Carbon::now()->toDateString();
                    $order->order_deliverd_time = Carbon::now()->toTimeString();
                    $order->save();

                    // Create a new Cred record
                    $cred = new Cred();
                    $cred->cred_in = $totalbox;
                    $cred->cred_out = $request->cred_out;
                    $cred->date = Carbon::now()->toDateString();
                    $cred->time = Carbon::now()->toTimeString();
                    $cred->driver_id = $driver_task->driver_id;
                    $cred->user_id = $driver_task->user_id;
                    $cred->save();

                    return response()->json([
                        'status' => 'success',
                        'cred' => $cred,
                        'message' => 'Alloted Task Approved successfully',
                        'taskDetails' => [
                            'task_id' => $driver_task->id,
                            'order_id' => $driver_task->order_id,
                            'total_boxes' => $totalbox,
                            'completion_date' => $driver_task->task_completed_date,
                            'completion_time' => $driver_task->task_completed_time,
                        ]
                    ], 200); // HTTP 200 OK
                }
            } catch (\Exception $e) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Task approval failed',
                    'error' => $e->getMessage(),
                ], 500); // HTTP 500 Internal Server Error
            }
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'User not authenticated',
            ], 401); // HTTP 401 Unauthorized
        }
    }
}
