<?php

namespace App\Http\Controllers\Api\AdminController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Api\Traits\ValidationTrait;
use App\Models\User;
use App\Models\Payment;
use App\Models\Order;

class AdminPaymentController extends Controller
{
    use ValidationTrait;

    public function paymentListing(Request $request)
    {
        $user = $this->validate_user($request->connection_id, $request->auth_code);
        if($user)
        {
            $query = Payment::select(
                'payments.user_id',
                'users.name as distributorName',
                'payments.created_at as payment_date',
                'payments.amount_paid',
                \DB::raw('(SELECT SUM(total_amount) FROM orders WHERE orders.user_id = payments.user_id AND orders.created_at <= payments.created_at AND orders.order_status IN (2, 3)) as orderTotalAmount'),
                \DB::raw('(SELECT SUM(amount_paid) FROM payments p2 WHERE p2.user_id = payments.user_id AND p2.created_at <= payments.created_at) as totalPaidTillDate'),
                \DB::raw('(SELECT SUM(total_amount) FROM orders WHERE orders.user_id = payments.user_id AND orders.created_at <= payments.created_at AND orders.order_status IN (2, 3)) - 
                         (SELECT SUM(amount_paid) FROM payments p2 WHERE p2.user_id = payments.user_id AND p2.created_at <= payments.created_at) as remainingAmount')
            )
            ->join('users', 'users.id', '=', 'payments.user_id')
            ->orderBy('payments.created_at', 'DESC');
                
            // Filter by distributor ID if provided
            if ($request->has('distributor_id')) {
                $query->where('payments.user_id', $request->distributor_id);
            }

            // Search by date if provided
            if ($request->has('start_date') && $request->has('end_date')) {
                $start_date = $request->input('start_date');
                $end_date = $request->input('end_date') . ' 23:59:59';
                $query->whereBetween('payments.created_at', [$start_date, $end_date]);
            }

            $payment = $query->get();

            // Modify summary calculation
            $summary = [
                'total_order_amount' => Order::whereIn('order_status', [2, 3])
                    ->when($request->has('distributor_id'), function($query) use ($request) {
                        $query->where('user_id', $request->distributor_id);
                    })
                    ->sum('total_amount'),
                'total_paid_amount' => Payment::when($request->has('distributor_id'), function($query) use ($request) {
                        $query->where('user_id', $request->distributor_id);
                    })
                    ->sum('amount_paid'),
            ];
            
            // Calculate the total due amount and round to 2 decimal places
            $summary['total_due_amount'] = number_format($summary['total_order_amount'] - $summary['total_paid_amount'], 2, '.', '');

            return response()->json([
                'status' => 'success',
                'summary' => $summary,
                'paymentHistory' => $payment,
                'message' => 'Payment retrieved successfully',
            ], 200);
        }
        else {
            return response()->json([
                'status' => 'error',
                'message' => 'User not authenticated',
            ], 401); // HTTP 401 Unauthorized
        }
    }

    public function distributorListing(Request $request)
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

    public function addPayment(Request $request)
    {
        $user = $this->validate_user($request->connection_id, $request->auth_code);
        if($user)
        {
            $totalAmount = Order::select('orders.total_amount as totalAmount')
            ->where('user_id', $request->distributor_id)->whereIn('order_status', [2, 3])->sum('total_amount');
            $paidAmount = Payment::where('user_id', $request->distributor_id)->sum('amount_paid');
            
            $remainingAmount = $totalAmount - $paidAmount;
            
            if($remainingAmount <= 0) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Payment cannot be accepted as the distributor has no due amount',
                ], 400);
            }
            
            if($request->paid_amount > $remainingAmount) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Payment amount cannot be greater than the remaining due amount of ' . $remainingAmount,
                ], 400);
            }
            
            $payment = new Payment();
            $payment->user_id = $request->distributor_id;
            $payment->amount_paid = $request->paid_amount;
            $payment->save();
            
            return response()->json([
                'status' => 'success',
                'message' => 'Payment added successfully',
                'payment' => $payment,
                ], 200);
        }
        else {
            return response()->json([
                'status' => 'error',
                'message' => 'User not authenticated',
            ], 401);
        }
    }
}
