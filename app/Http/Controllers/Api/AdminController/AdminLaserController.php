<?php

namespace App\Http\Controllers\Api\AdminController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Api\Traits\ValidationTrait;
use App\Models\Bill;
use Carbon\Carbon;
use App\Models\OrderDetail;
use App\Models\User;

class AdminLaserController extends Controller
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

    public function laserListing(Request $request)
    {
        $user = $this->validate_user($request->connection_id, $request->auth_code);
        if($user)
        {
            $query = Bill::select('bills.bill_no', 'bills.order_no', 'bills.bill_date', 'bills.bill_time', 
                                 'orders.total_amount', 'orders.order_status', 'users.name')
                    ->join('orders', 'bills.order_id', '=', 'orders.id')
                    ->join('users', 'bills.user_id', '=', 'users.id');

            // Apply date filter if provided
            if ($request->has('date')) {
                $date = Carbon::createFromFormat('m-Y', $request->date);
                $query->whereMonth('bill_date', $date->format('m'))
                      ->whereYear('bill_date', $date->format('Y'));
            }

            // Apply distributor filter if provided
            if ($request->has('distributor_id')) {
                $query->where('bills.user_id', $request->distributor_id);
            }

            $bills = $query->orderBy('bills.id','desc')->get();

            return response()->json([
                'status' => 'success',
                'lasers' => $bills,
                'message' => 'Bills retrieved successfully',
            ], 200);
        }
        else{
            return response()->json([
                'status' => 'error',
                'message' => 'User not authenticated',
            ], 401); // HTTP 401 Unauthorized
        }
    }
}
