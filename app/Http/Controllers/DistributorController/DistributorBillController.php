<?php

namespace App\Http\Controllers\DistributorController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Bill;
use App\Models\Order;
use App\Models\OrderDetail;
use Illuminate\Support\Facades\Auth;
use PDF;

class DistributorBillController extends Controller
{
    public function index()
    {
        return view('Distributor.Bills.bills_listing');
    }

    public function listing(Request $request)
    {
        if($request->search){
            $bills=Bill::join('orders','bills.order_id','=','orders.id')->where('bills.user_id',Auth::user()->id)
            ->where(function($query) use ($request) {
                $query->where('orders.order_no', 'LIKE', '%' . $request->search . '%')
                ->orWhere('orders.total_amount','LIKE','%'. $request->search.'%')
                ->orWhere('bills.bill_no','LIKE','%'. $request->search.'%');
            })->orderBy('bills.id','DESC')->paginate(10);
            if(count($bills)>0){
                return response()->json($bills);
            }
            else{
                return response()->json([['bills'=>null]]);
            }

        }
        else if($request->searchDate){
            $bills=Bill::join('orders','bills.order_id','=','orders.id')->where('bills.user_id',Auth::user()->id)
            ->where(function($query) use ($request) {
                $query->where('orders.order_date', 'LIKE', '%' . $request->searchDate . '%');
            })->orderBy('bills.id','DESC')->paginate(10);
            if(count($bills)>0){
                return response()->json($bills);
            }
            else{
                return response()->json([['bills'=>null]]);
            }
        }
        else{
                $bills=Bill::join('orders','bills.order_id','=','orders.id')->where('bills.user_id',Auth::user()->id)->orderBy('bills.id','DESC')->paginate(10);
                // dd($bills);
                if(count($bills)>0){
                    return response()->json($bills);
                }
                else{
                    return response()->json(['bills'=>null]);
                }
            }
    }

    public function billDetail($id)
    {
        $order=Order::select('orders.*','bills.bill_no')->join('bills','orders.id','=','bills.order_id')->where('orders.id',$id)->first();
        if($order){
            $orderDetail=OrderDetail::join('bills','order_details.order_id','bills.order_id')
            ->join('orders','order_details.order_id','=','orders.id')
            ->where('order_details.order_id',$id)->get();
        }
        return view('Distributor.Bills.bills_detail')->with(['orderDetail'=>$orderDetail,'order'=>$order]);
    }
    public function invoicePdf($id)
    {
        $order=Order::select('orders.*','bills.bill_no')->join('bills','orders.id','=','bills.order_id')->where('orders.id',$id)->first();
        $orderDetail = OrderDetail::join('bills', 'order_details.order_id', 'bills.order_id')
            ->join('orders', 'order_details.order_id', '=', 'orders.id')
            ->where('order_details.order_id', $id)
            ->get();

        // Generate PDF
        $pdf = PDF::loadView('Distributor.Bills.invoice_pdf', [
            'order' => $order,
            'orderDetail' => $orderDetail
        ]);

        // Download the PDF file
        return $pdf->download('invoice_' . $order->bill_no . '.pdf');
    }
}
