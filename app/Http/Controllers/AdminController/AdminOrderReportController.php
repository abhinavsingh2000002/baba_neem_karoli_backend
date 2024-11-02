<?php

namespace App\Http\Controllers\AdminController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use PDF;
use App\Exports\OrderReportExport;
use Maatwebsite\Excel\Facades\Excel;

class AdminOrderReportController extends Controller
{
    public function index()
    {
        return view('Backend.Report.report_index');
    }

    public function listing(Request $request)
    {
        $all_product=Product::where('status',1)->get();
        $order = Order::with(['orderDetails', 'user'])
        ->where('order_date', '=', $request->date)
        ->whereHas('user', function ($query) {
            $query->where('status', 1);
        })
        ->get();
        if(count($order)==0){
            return response()->json(['failed'=>'No Order Found !']);
        }
        return view('Backend.Report.report_search')->with(['all_product'=>$all_product,'order'=>$order]);
    }

    public function reportpdf(Request $request)
    {
        $all_product=Product::where('status',1)->get();
        $order = Order::with(['orderDetails', 'user'])
        ->where('order_date', '=', $request->date)
        ->whereHas('user', function ($query) {
            $query->where('status', 1);
        })
        ->get();
        if(count($order)===0){
            return response()->json(['failed'=>'No Order Found !']);
        }
        $pdf = PDF::loadView('Backend.Report.report_pdf', [
            'all_product' => $all_product,
            'order' => $order,
        ])->setPaper('a3', 'landscape'); // Set paper size to A4 landscape

        return $pdf->download('Report_' . $request->date . '.pdf');
    }

    public function reportExcel(Request $request)
    {
        return Excel::download(new OrderReportExport($request->date), 'Report_' . $request->date . '.xlsx');
    }
}
