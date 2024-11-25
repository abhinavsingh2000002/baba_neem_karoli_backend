<?php

namespace App\Http\Controllers\Api\DistributorController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Api\Traits\ValidationTrait;
use App\Models\Bill;
use Carbon\Carbon;
use App\Models\OrderDetail;
use App\Models\Order;
use App\Models\Payment;

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
            $bills = Bill::select('bills.bill_no','bills.order_no','bills.bill_date','bills.bill_time','orders.total_amount','orders.total_amount','orders.order_status','users.name')
            ->join('orders','bills.order_id','=','orders.id')
            ->join('users','bills.user_id','=','users.id')
            ->whereMonth('bill_date', $month)
              ->whereYear('bill_date', $year)
              ->where('bills.user_id', '=', $user)
              ->whereIn('orders.order_status',[2,3])
              ->get();
              $orderTotalAmount = Order::where('user_id', $user)
              ->whereIn('order_status', [2, 3])
              ->whereMonth('order_date', $month)
              ->whereYear('order_date', $year)
              ->sum('total_amount');

              $paidAmount = Payment::where('user_id', $user)
              ->whereMonth('created_at', $month)
              ->whereYear('created_at', $year)
              ->sum('amount_paid');
              
              $differenceAmount = round($orderTotalAmount - $paidAmount, 2);
              $advanceAmount = 0;
              $dueAmount = 0;
              if ($differenceAmount < 0) {
                $advanceAmount = number_format(abs($differenceAmount), 2, '.', '');
                $dueAmount = "0.00";
              } else {
                $dueAmount = number_format($differenceAmount, 2, '.', '');
                $advanceAmount = "0.00";
              }
              return response()->json([
                'status' => 'success',
                'lasers'=>$bills,
                'message' => 'Bills retrieved successfully',
                'orderTotalAmount' => $orderTotalAmount,
                'paidAmount' => $paidAmount,
                'advanceAmount' => $advanceAmount,
                'remainingAmount' => $dueAmount,
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
