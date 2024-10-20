<?php

namespace App\Http\Controllers\DistributorController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ShoppingCart;
use App\Models\Order;
use App\Models\Bill;
use App\Models\OrderDetail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Carbon\Carbon;
use PDF;

class OrderPlacedController extends Controller
{
    public function index()
    {
        return view('Distributor.Order.order_placed');
    }
    public function oderPlaced(Request $request)
    {
        $total_order=ShoppingCart::select('shopping_carts.quantity','products.*','map_product_prices.price')->join('products','shopping_carts.product_id','=','products.id')
        ->join('map_product_prices','shopping_carts.product_id','map_product_prices.product_id')->where('shopping_carts.user_id',Auth::user()->id)
        ->where('map_product_prices.user_id',Auth::user()->id)
        ->where('products.status','=',1)
        ->where('map_product_prices.status','=',1)->get();
        // dd($total_order);
        $totalAmount=[];
        foreach($total_order as $tot_order)
        {
            $price = floatval($tot_order->price);
            $product_quantity = floatval($tot_order->quantity);
            $totalAmount[] = $price * $product_quantity;
        }
        $totalAmount=array_sum($totalAmount);
        if(count($total_order)>0){
            $order=new Order();
            do {
                $orderNo = mt_rand(1000000000, 9999999999);
            } while (Order::where('order_no', $orderNo)->exists());
            $order->order_no = $orderNo;
            $order->user_id=Auth::user()->id;
            $order->total_amount=$totalAmount;
            $order->order_date=Carbon::now()->toDateString(); // YYYY-MM-DD format
            $order->order_time=Carbon::now()->toTimeString(); // Will store time in HH:MM:SS format
            $order->save();
        }
        else{
            return response()->json(['failed'=>'Please Add Item to the cart']);
        }
        if($order){
            $bill=new Bill();
            do {
                $billNo = mt_rand(1000000000, 9999999999);
            } while (Bill::where('bill_no', $billNo)->exists());
            $bill->bill_no=$billNo;
            $bill->user_id=Auth::user()->id;
            $bill->order_id=$order->id;
            $bill->order_no=$order->order_no;
            $bill->bill_date=Carbon::now()->toDateString(); // YYYY-MM-DD format
            $bill->bill_time=Carbon::now()->toTimeString(); // Will store time in HH:MM:SS format
            $bill->save();
        }
        if($order){
            foreach($total_order as $total_order_one)
            {
                // dd($total_order_one);

                $orderDetails=new OrderDetail();
                $orderDetails->order_id=$order->id;
                $orderDetails->order_no=$order->order_no;
                $orderDetails->user_id=Auth::user()->id;
                $orderDetails->product_no=$total_order_one->product_no;
                $orderDetails->product_name=$total_order_one->product_name;
                $orderDetails->product_no=$total_order_one->product_no;
                $orderDetails->product_name=$total_order_one->product_name;
                $orderDetails->company_name=$total_order_one->company_name;
                $orderDetails->product_image=$total_order_one->product_image;
                $orderDetails->product_description=$total_order_one->product_description;
                $orderDetails->product_weight=$total_order_one->product_quantity;
                $orderDetails->product_quantity=$total_order_one->quantity;
                $orderDetails->item_per_cred=$total_order_one->item_per_cred;
                $price = floatval($total_order_one->price);
                $product_quantity = floatval($total_order_one->quantity);
                $orderDetails->amount = $price * $product_quantity;
                // dd($price,$product_quantity);
                $orderDetails->save();
            }
        return response()->json(['success'=>'Order Placed Successfully']);
        }
    }

    public function listing(Request $request)
    {

        if($request->search) {
            $order = Order::where('user_id', Auth::user()->id)
                ->where(function($query) use ($request) {
                    $query->where('order_no', 'LIKE', '%' . $request->search . '%')
                          ->orWhere('total_amount','LIKE','%'. $request->search.'%');
                })
                ->orderBy('id', 'DESC')
                ->paginate(10);
            if(count($order)>0){
                return response()->json($order);
            }
            else{
                return response()->json([['order'=>null]]);
            }
        }
        else if($request->searchDate){
            $order=Order::where('user_id',Auth::user()->id)->where('order_date','LIKE','%'.$request->searchDate.'%')->orderBy('id','DESC')->paginate(10);
            if(count($order)>0){
                return response()->json($order);
            }
            else{
                return response()->json([['order'=>null]]);
            }
        }

        else{
            $order=Order::where('user_id',Auth::user()->id)->orderBy('id','DESC')->paginate(10);
            if(count($order)>0){
                return response()->json($order);
            }
            else{
                return response()->json([['order'=>null]]);
            }
        }

    }

    public function listingDetail($id)
    {
        $order=Order::select('orders.*','bills.bill_no')->join('bills','orders.id','=','bills.order_id')->where('orders.id',$id)->first();
        if($order){
            $orderDetail=OrderDetail::join('bills','order_details.order_id','bills.order_id')
            ->join('orders','order_details.order_id','=','orders.id')
            ->where('order_details.order_id',$id)->get();
    }
        return view('Distributor.Order.order_detail')->with(['orderDetail'=>$orderDetail,'order'=>$order]);
    }


    public function invoicePdf($id)
    {
        $order=Order::select('orders.*','bills.bill_no')->join('bills','orders.id','=','bills.order_id')->where('orders.id',$id)->first();
        $orderDetail = OrderDetail::join('bills', 'order_details.order_id', 'bills.order_id')
            ->join('orders', 'order_details.order_id', '=', 'orders.id')
            ->where('order_details.order_id', $id)
            ->get();

        // Generate PDF
        $pdf = PDF::loadView('Distributor.Order.invoice_pdf', [
            'order' => $order,
            'orderDetail' => $orderDetail
        ]);

        // Download the PDF file
        return $pdf->download('invoice_' . $order->order_no . '.pdf');
    }
}
