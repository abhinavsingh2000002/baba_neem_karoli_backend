<?php

namespace App\Http\Controllers\Api\AdminController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Api\Traits\ValidationTrait;
use App\Models\Bill;
use Carbon\Carbon;
use App\Models\OrderDetail;
use App\Models\User;
use App\Models\Order;
use App\Models\Payment;

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
                    ->join('users', 'bills.user_id', '=', 'users.id')
                    ->whereIn('orders.order_status',[2,3]);

            $orderTotalAmountQuery = Order::whereIn('order_status',[2,3]);
            $paidAmountQuery = Payment::select('amount_paid');

            // Apply date filter if provided
            if ($request->has('date')) {
                $date = Carbon::createFromFormat('m-Y', $request->date);
                $query->whereMonth('bills.bill_date', $date->format('m'))
                      ->whereYear('bills.bill_date', $date->format('Y'));
                $orderTotalAmountQuery->whereMonth('created_at', $date->format('m'))
                ->whereYear('created_at', $date->format('Y'));
                $paidAmountQuery->whereMonth('created_at', $date->format('m'))
                ->whereYear('created_at', $date->format('Y'));
            }

            // Apply distributor filter if provided
            if ($request->has('distributor_id')) {
                $distributorId = $request->distributor_id;
                
                // Add distributor filters to all queries
                $query->where('bills.user_id', $distributorId);
                $orderTotalAmountQuery->where('user_id', $distributorId);
                $paidAmountQuery->where('user_id', $distributorId);
                
                $bills = $query->orderBy('bills.id', 'desc')->get();
                
                // Calculate amounts for specific distributor
                $orderTotalAmount = $orderTotalAmountQuery->sum('total_amount');
                $paidAmount = $paidAmountQuery->sum('amount_paid');
                $remainingAmount = $orderTotalAmount - $paidAmount;
                
                $advanceAmount = "0.00";
                $dueAmount = number_format($remainingAmount, 2, '.', '');
                
                if ($remainingAmount < 0) {
                    $advanceAmount = number_format(abs($remainingAmount), 2, '.', '');
                    $dueAmount = "0.00";
                }
                
                $orderTotalAmount = number_format($orderTotalAmount, 2, '.', '');
                $paidAmount = number_format($paidAmount, 2, '.', '');
            } else {
                // All distributors case
                $bills = $query->orderBy('bills.id','desc')->get();
                $distributors = User::where('role_id', 2)->where('status', 1)->get();
                
                $orderTotalAmount = 0;
                $paidAmount = 0;
                $totalAdvanceAmount = 0;
                $totalDueAmount = 0;

                foreach ($distributors as $distributor) {
                    $distributorOrderTotal = clone $orderTotalAmountQuery;
                    $distributorPaidAmount = clone $paidAmountQuery;

                    $distributorTotal = $distributorOrderTotal->where('user_id', $distributor->id)->sum('total_amount');
                    $distributorPaid = $distributorPaidAmount->where('user_id', $distributor->id)->sum('amount_paid');
                    $distributorRemaining = $distributorTotal - $distributorPaid;

                    $orderTotalAmount += $distributorTotal;
                    $paidAmount += $distributorPaid;

                    if ($distributorRemaining < 0) {
                        $totalAdvanceAmount += abs($distributorRemaining);
                    } else {
                        $totalDueAmount += $distributorRemaining;
                    }
                }

                $orderTotalAmount = number_format($orderTotalAmount, 2, '.', '');
                $paidAmount = number_format($paidAmount, 2, '.', '');
                $advanceAmount = number_format($totalAdvanceAmount, 2, '.', '');
                $dueAmount = number_format($totalDueAmount, 2, '.', '');
            }

            return response()->json([
                'status' => 'success',
                'lasers' => $bills,
                'orderTotalAmount' => $orderTotalAmount,
                'paidAmount' => $paidAmount,
                'dueAmount' => $dueAmount,
                'advanceAmount' => $advanceAmount,
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
