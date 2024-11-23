<?php

namespace App\Http\Controllers\Api\DistributorController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Payment;
use App\Api\Traits\ValidationTrait;
use App\Models\User;
use App\Models\Order;

class DistributorPaymentController extends Controller
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
                \DB::raw('(SELECT COALESCE(SUM(total_amount), 0) FROM orders WHERE orders.user_id = payments.user_id AND orders.created_at <= payments.created_at AND orders.order_status IN (2, 3)) as orderTotalAmount'),
                \DB::raw('(SELECT COALESCE(SUM(amount_paid), 0) FROM payments p2 WHERE p2.user_id = payments.user_id AND p2.created_at <= payments.created_at) as totalPaidTillDate'),
                \DB::raw('CASE 
                    WHEN COALESCE((SELECT SUM(total_amount) FROM orders WHERE orders.user_id = payments.user_id AND orders.created_at <= payments.created_at AND orders.order_status IN (2, 3)), 0) >
                         COALESCE((SELECT SUM(amount_paid) FROM payments p2 WHERE p2.user_id = payments.user_id AND p2.created_at <= payments.created_at), 0)
                    THEN COALESCE((SELECT SUM(total_amount) FROM orders WHERE orders.user_id = payments.user_id AND orders.created_at <= payments.created_at AND orders.order_status IN (2, 3)), 0) - 
                         COALESCE((SELECT SUM(amount_paid) FROM payments p2 WHERE p2.user_id = payments.user_id AND p2.created_at <= payments.created_at), 0)
                    ELSE 0
                END as remainingAmount'),
                \DB::raw('CASE 
                    WHEN COALESCE((SELECT SUM(amount_paid) FROM payments p2 WHERE p2.user_id = payments.user_id AND p2.created_at <= payments.created_at), 0) >
                         COALESCE((SELECT SUM(total_amount) FROM orders WHERE orders.user_id = payments.user_id AND orders.created_at <= payments.created_at AND orders.order_status IN (2, 3)), 0)
                    THEN COALESCE((SELECT SUM(amount_paid) FROM payments p2 WHERE p2.user_id = payments.user_id AND p2.created_at <= payments.created_at), 0) -
                         COALESCE((SELECT SUM(total_amount) FROM orders WHERE orders.user_id = payments.user_id AND orders.created_at <= payments.created_at AND orders.order_status IN (2, 3)), 0)
                    ELSE 0
                END as advanceAmount')
            )
            ->join('users', 'users.id', '=', 'payments.user_id')
            ->where('payments.user_id', $user)
            ->orderBy('payments.created_at', 'DESC');

            // Search by date if provided
            if ($request->has('start_date') && $request->has('end_date')) {
                $start_date = $request->input('start_date');
                $end_date = $request->input('end_date') . ' 23:59:59';
                $query->whereBetween('payments.created_at', [$start_date, $end_date]);
            }
            
            $payment = $query->get();

            // Modify summary calculation
            $summary = [
                'total_order_amount' => Order::where('user_id', $user)
                ->whereIn('order_status', [2, 3])
                    ->sum('total_amount'),
                'total_paid_amount' => Payment::where('user_id', $user)
                    ->sum('amount_paid'),
            ];

            // Calculate the difference
            $difference = $summary['total_order_amount'] - $summary['total_paid_amount'];
            
            if ($difference < 0) {
                $summary['advance_amount'] = number_format(abs($difference), 2, '.', '');
                $summary['total_due_amount'] = "0.00";
            } else {
                $summary['advance_amount'] = "0.00";
                $summary['total_due_amount'] = number_format($difference, 2, '.', '');
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
}
