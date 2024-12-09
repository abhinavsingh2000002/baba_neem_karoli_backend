<?php

namespace App\Http\Controllers\Api\AdminController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Api\Traits\ValidationTrait;
use App\Models\User;
use App\Models\Payment;
use App\Models\Order;
use App\Models\SchemeCategory;

class AdminPaymentController extends Controller
{
    use ValidationTrait;

    public function paymentListing(Request $request)
    {
        $user = $this->validate_user($request->connection_id, $request->auth_code);
        if($user)
        {
            $query = Payment::select(
                'payments.id as paymentId',
                'payments.user_id',
                'users.name as distributorName',
                'payments.created_at as payment_date',
                'payments.payment_type',
                'scheme_categorys.title as SchemeName',
                'payments.amount_paid',
                \DB::raw('COALESCE((SELECT SUM(total_amount) FROM orders WHERE orders.user_id = payments.user_id AND orders.created_at <= payments.created_at AND orders.order_status IN (2, 3)), 0) as orderTotalAmount'),
                \DB::raw('COALESCE((SELECT SUM(amount_paid) FROM payments p2 WHERE p2.user_id = payments.user_id AND p2.created_at <= payments.created_at), 0) as totalPaidTillDate'),
                \DB::raw('CASE
                    WHEN (COALESCE((SELECT SUM(total_amount) FROM orders WHERE orders.user_id = payments.user_id AND orders.created_at <= payments.created_at AND orders.order_status IN (2, 3)), 0) -
                         COALESCE((SELECT SUM(amount_paid) FROM payments p2 WHERE p2.user_id = payments.user_id AND p2.created_at <= payments.created_at), 0)) < 0
                    THEN 0
                    ELSE (COALESCE((SELECT SUM(total_amount) FROM orders WHERE orders.user_id = payments.user_id AND orders.created_at <= payments.created_at AND orders.order_status IN (2, 3)), 0) -
                         COALESCE((SELECT SUM(amount_paid) FROM payments p2 WHERE p2.user_id = payments.user_id AND p2.created_at <= payments.created_at), 0))
                    END as remainingAmount'),
                \DB::raw('CASE
                    WHEN (COALESCE((SELECT SUM(total_amount) FROM orders WHERE orders.user_id = payments.user_id AND orders.created_at <= payments.created_at AND orders.order_status IN (2, 3)), 0) -
                         COALESCE((SELECT SUM(amount_paid) FROM payments p2 WHERE p2.user_id = payments.user_id AND p2.created_at <= payments.created_at), 0)) < 0
                    THEN ABS(COALESCE((SELECT SUM(total_amount) FROM orders WHERE orders.user_id = payments.user_id AND orders.created_at <= payments.created_at AND orders.order_status IN (2, 3)), 0) -
                         COALESCE((SELECT SUM(amount_paid) FROM payments p2 WHERE p2.user_id = payments.user_id AND p2.created_at <= payments.created_at), 0))
                    ELSE 0
                    END as advanceAmount')
            )
            ->join('users', 'users.id', '=', 'payments.user_id')
            ->leftjoin('scheme_categorys','payments.scheme_category_id','=','scheme_categorys.id')
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

            if ($request->has('distributor_id')) {
                // Single distributor logic (existing)
                $summary = [
                    'total_order_amount' => Order::whereIn('order_status', [2, 3])
                        ->where('user_id', $request->distributor_id)
                        ->sum('total_amount'),
                    'total_paid_amount' => Payment::where('user_id', $request->distributor_id)
                        ->sum('amount_paid'),
                ];

                $difference = $summary['total_order_amount'] - $summary['total_paid_amount'];

                if ($difference < 0) {
                    $summary['advance_amount'] = number_format(abs($difference), 2, '.', '');
                    $summary['total_due_amount'] = "0.00";
                } else {
                    $summary['advance_amount'] = "0.00";
                    $summary['total_due_amount'] = number_format($difference, 2, '.', '');
                }
            } else {
                // All distributors logic
                $distributors = User::where('role_id', 2)->where('status', 1)->get();
                $totalAdvance = 0;
                $totalDue = 0;

                foreach ($distributors as $distributor) {
                    $orderAmount = Order::whereIn('order_status', [2, 3])
                        ->where('user_id', $distributor->id)
                        ->sum('total_amount');

                    $paidAmount = Payment::where('user_id', $distributor->id)
                        ->sum('amount_paid');

                    $difference = $orderAmount - $paidAmount;

                    if ($difference < 0) {
                        $totalAdvance += abs($difference);
                    } else {
                        $totalDue += $difference;
                    }
                }

                $summary = [
                    'total_order_amount' => Order::whereIn('order_status', [2, 3])->sum('total_amount'),
                    'total_paid_amount' => Payment::sum('amount_paid'),
                    'advance_amount' => number_format($totalAdvance, 2, '.', ''),
                    'total_due_amount' => number_format($totalDue, 2, '.', '')
                ];
            }

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


    public function schemeCategoryListing(Request $request)
    {
        $user = $this->validate_user($request->connection_id, $request->auth_code);
        if($user)
        {
        $schemeCategory=SchemeCategory::where('status','=',1)->get();
        return response()->json([
            'status' => 'success',
            'schemeCategory' => $schemeCategory,
            'message' => 'schemeCategory retrieved successfully',
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

            $payment = new Payment();
            $payment->user_id = $request->distributor_id;
            $payment->amount_paid = $request->paid_amount;
            $payment->scheme_category_id=$request->scheme_category_id;
            $payment->payment_type = $request->scheme_category_id ? 0 : 1;
            $payment->save();

            $message = $remainingAmount <= 0 ?
                'Advance payment added successfully' :
                'Payment added successfully';

            return response()->json([
                'status' => 'success',
                'message' => $message,
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

    public function updatePayment(Request $request)
    {
        $user = $this->validate_user($request->connection_id, $request->auth_code);
        if($user)
        {
            $payment = Payment::find($request->payment_id);
            if(!$payment) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Payment not found',
                ], 404);
            }
            $payment->user_id = $request->distributor_id;
            $payment->amount_paid = $request->paid_amount;
            $payment->scheme_category_id=$request->scheme_category_id;
            $payment->payment_type = $request->scheme_category_id ? 0 : 1;
            $payment->save();
            return response()->json([
                'status' => 'success',
                'message' => 'Payment updated successfully',
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

    public function deletePayment(Request $request)
    {
        $user = $this->validate_user($request->connection_id, $request->auth_code);
        if($user)
        {
            $payment = Payment::find($request->payment_id);
            $payment->delete();
            return response()->json([
                'status' => 'success',
                'message' => 'Payment deleted successfully',
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
