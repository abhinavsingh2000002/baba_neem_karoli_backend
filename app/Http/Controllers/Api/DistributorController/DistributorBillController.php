<?php

namespace App\Http\Controllers\Api\DistributorController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Api\Traits\ValidationTrait;
use App\Models\Bill;
use App\Models\Order;
use App\Models\OrderDetail;
use PDF;

class DistributorBillController extends Controller
{
    use ValidationTrait;

    public function billListing(Request $request)
    {
       // Validate user using connection ID and auth code
        $user = $this->validate_user($request->connection_id, $request->auth_code);

        if ($user) {
            // Initialize the query for bills
            $query = Bill::where('user_id', '=', $user);

            // Check if a date is provided in the request
            if ($request->has('date')) {
                // Validate and format the date if needed
                $date = $request->input('date');
                $query->whereDate('bill_date', $date);
            }

            // Retrieve the bills based on the query
            $bills = $query->get();

            // Add the count of ordered items to each bill
            $bills->map(function ($bill) {
                // Count the number of items in OrderDetail for the bill's order_id
                $bill->noOfOrderedItems = OrderDetail::where('order_id', $bill->order_id)->count();
                return $bill;
            });

            return response()->json([
                'status' => 'success',
                'bills' => $bills,
                'message' => 'Bills retrieved successfully',
            ], 200); // HTTP 200 OK
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'User not authenticated',
            ], 401); // HTTP 401 Unauthorized
        }

    }

    public function billDetailListing(Request $request)
    {
        // Validate user using connection ID and auth code
        $user = $this->validate_user($request->connection_id, $request->auth_code);
        if($user)
        {
            $billsDetail = Bill::select(
                'bills.*',
                'orders.total_amount',
                'order_details.product_name',
                'order_details.company_name',
                'order_details.product_weight',
                'order_details.product_quantity',
                'order_details.item_per_cred',
                'order_details.amount',
                'users.name',
                'users.address'
            )
            ->join('orders', 'bills.order_id', '=', 'orders.id')
            ->join('order_details', 'bills.order_id', '=', 'order_details.order_id')
            ->join('users', 'bills.user_id', '=', 'users.id')
            ->where('bills.id', '=', $request->bill_id)
            ->get();

            return response()->json([
                'status' => 'success',
                'totalAmount' => $billsDetail[0]->total_amount,
                'userName' => $billsDetail[0]->name,
                'userAddress' => $billsDetail[0]->address,
                'invoiceNo' => $billsDetail[0]->bill_no,
                'invoiceDateTime' => $billsDetail[0]->bill_date . ',' . date('h:i A', strtotime($billsDetail[0]->bill_time)), // Corrected line
                'billsDetail' => $billsDetail,
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

    public function invoicePdf(Request $request)
    {
        // Validate user using connection ID and auth code
        $user = $this->validate_user($request->connection_id, $request->auth_code);
        if($user)
        {
            $order=Order::select('orders.*','bills.bill_no')->join('bills','orders.id','=','bills.order_id')->where('orders.id',$request->order_id)->first();
            $orderDetail = OrderDetail::join('bills', 'order_details.order_id', 'bills.order_id')
                ->join('orders', 'order_details.order_id', '=', 'orders.id')
                ->where('order_details.order_id', $request->order_id)
                ->get();

            // Generate PDF
            $pdf = PDF::loadView('Distributor.Bills.invoice_pdf', [
                'order' => $order,
                'orderDetail' => $orderDetail
            ]);

            // Download the PDF file
            return $pdf->download('invoice_' . $order->bill_no . '.pdf');
        }
        else{
            return response()->json([
                'status' => 'error',
                'message' => 'User not authenticated',
            ], 401); // HTTP 401 Unauthorized
        }
    }
}
