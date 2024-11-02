<?php

namespace App\Http\Controllers\Api\DistributorController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Api\Traits\ValidationTrait;
use App\Models\Bill;
use Carbon\Carbon;
use App\Models\OrderDetail;

class DistributorLaserController extends Controller
{
    use ValidationTrait;

    public function laserListing(Request $request)
    {
        $user = $this->validate_user($request->connection_id, $request->auth_code);
        if($user)
        {
            $date = Carbon::createFromFormat('m-Y', $request->date);
            $month = $date->format('m');
            $year = $date->format('Y');
            $bills = Bill::select('bills.bill_no','bills.bill_date','bills.bill_time','orders.total_amount','orders.total_amount')->join('orders','bills.order_id','=','orders.id')->whereMonth('bill_date', $month)
              ->whereYear('bill_date', $year)
              ->where('bills.user_id', '=', $user)
              ->get();
              return response()->json([
                'status' => 'success',
                'lasers'=>$bills,
                'message' => 'Bills retrieved successfully',
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
