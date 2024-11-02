<?php

namespace App\Http\Controllers\Api\DriverController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Api\Traits\ValidationTrait;
use App\Models\DriverTask;

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
}
