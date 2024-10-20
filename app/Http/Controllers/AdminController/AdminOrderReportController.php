<?php

namespace App\Http\Controllers\AdminController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;

class AdminOrderReportController extends Controller
{
    public function index()
    {
        $all_product=Product::where('status',1)->orderBy('product_no')->get();
        $order_detail=OrderDetail::select('users.name','users.address','order_details.*')->join('orders','order_details.order_id','=','orders.id')
        ->join('users','order_details.user_id','=','users.id')->where('users.status','=',1)->orderBy('product_no')->get();
        // dd($order_detail,$all_product);
        return view('Backend.Report.report_search')->with(['all_product'=>$all_product,'order_detail'=>$order_detail]);
        return view('Backend.Report.report_index');
    }

    public function listing(Request $request)
    {
        $orders=Order::where('order_date','=',$request->date)->get();
        return view('Backend.Report.report_search',$orders);
    }
}
