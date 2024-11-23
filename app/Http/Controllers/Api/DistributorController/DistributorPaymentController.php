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
                \DB::raw('(SELECT SUM(total_amount) FROM orders WHERE orders.user_id = payments.user_id AND orders.created_at <= payments.created_at AND orders.order_status IN (2, 3)) as orderTotalAmount'),
                \DB::raw('(SELECT SUM(amount_paid) FROM payments p2 WHERE p2.user_id = payments.user_id AND p2.created_at <= payments.created_at) as totalPaidTillDate'),
                \DB::raw('(SELECT SUM(total_amount) FROM orders WHERE orders.user_id = payments.user_id AND orders.created_at <= payments.created_at AND orders.order_status IN (2, 3)) - 
                         (SELECT SUM(amount_paid) FROM payments p2 WHERE p2.user_id = payments.user_id AND p2.created_at <= payments.created_at) as remainingAmount')
            )
            ->join('users', 'users.id', '=', 'payments.user_id')
            ->where('payments.user_id', $user)
            ->orderBy('payments.created_at', 'DESC');

            // Search by date if provided
            if ($request->has('date')) {
                $query->whereRaw('DATE(payments.created_at) LIKE ?', ['%' . $request->date . '%']);
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
}
